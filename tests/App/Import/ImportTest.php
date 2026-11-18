<?php

declare(strict_types=1);

namespace App\Tests\App\Import;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Tag;
use App\Enum\DatumTypeEnum;
use App\Enum\VisibilityEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ImportTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function test_can_import_items_from_csv(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $importId = $this->uploadAndMap($collection, "name,Author,Year\nFrieren #1,Abe Tsukasa,2020\nDune,Frank Herbert,1965\n", 0, null, [
            $this->mapping(1, DatumTypeEnum::TYPE_TEXT, 'Author', 1),
            $this->mapping(2, DatumTypeEnum::TYPE_NUMBER, 'Year', 2),
        ]);
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));

        $this->executeImport($collection, $importId);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $items = $this->findItems();
        $this->assertCount(2, $items);
        $this->assertSame('Frieren #1', $items['Frieren #1']->getName());
        $this->assertSame('Abe Tsukasa', $items['Frieren #1']->getDatumByLabel('Author')?->getValue());
        $this->assertSame('1965', $items['Dune']->getDatumByLabel('Year')?->getValue());
    }

    public function test_import_creates_each_new_tag_once_and_trims_labels(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $importId = $this->uploadAndMap($collection, "name,Author\nA,\"New tag, Other tag\"\nB,New tag\n", 0, null, [
            $this->mapping(1, DatumTypeEnum::TYPE_TEXT, 'Author', 1, true),
        ]);
        $this->executeImport($collection, $importId);

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->clear();
        $labels = array_map(static fn (Tag $tag): string => $tag->getLabel(), $em->getRepository(Tag::class)->findAll());
        sort($labels);
        $this->assertSame(['New tag', 'Other tag'], $labels);
        $this->assertCount(2, $this->findItems()['A']->getTags());
        $this->assertCount(1, $this->findItems()['B']->getTags());
    }

    public function test_preview_displays_image_from_first_column(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $this->uploadAndMap($collection, "image,name\nhttps://example.com/cover.png,A\n", 1, 0, []);
        $crawler = $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSame('https://example.com/cover.png', $crawler->filter('tbody img')->attr('src'));
    }

    public function test_upload_page_displays_max_upload_size(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $crawler = $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import');

        $this->assertResponseIsSuccessful();
        $this->assertStringStartsWith('Maximum file size: ', $crawler->filter('form[name=import_upload] .helper-text')->text());
    }

    public function test_mapping_prefills_base_fields_from_headers(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $path = tempnam(sys_get_temp_dir(), 'import') . '.csv';
        file_put_contents($path, "Author,TAGS, Image ,quantity,Visibility,Name\nAbe Tsukasa,Manga,,1,public,Frieren #1\n");

        $crawler = $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import');
        $form = $crawler->filter('form[name=import_upload]')->form();
        $form['import_upload[fileFile]']->upload($path);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $values = $crawler->filter('form[name=import_mapping]')->form()->getValues();
        $this->assertSame('5', $values['import_mapping[nameIndex]']);
        $this->assertSame('2', $values['import_mapping[imageIndex]']);
        $this->assertSame('3', $values['import_mapping[quantityIndex]']);
        $this->assertSame('1', $values['import_mapping[tagsIndex]']);
        $this->assertSame('4', $values['import_mapping[visibilityIndex]']);

        $this->assertCount(1, $crawler->filter('.mapping'));
        $this->assertSame('0', $values['import_mapping[mapping][0][columnIndex]']);
        $this->assertSame('Author', $values['import_mapping[mapping][0][datumLabel]']);
        $this->assertSame(DatumTypeEnum::TYPE_TEXT, $values['import_mapping[mapping][0][datumType]']);
        $this->assertSame(VisibilityEnum::VISIBILITY_PUBLIC, $values['import_mapping[mapping][0][datumVisibility]']);
    }

    public function test_prefilled_mapping_can_be_submitted_as_is(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $path = tempnam(sys_get_temp_dir(), 'import') . '.csv';
        file_put_contents($path, "Name,Author,Year\nFrieren #1,Abe Tsukasa,2020\n");

        $crawler = $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import');
        $form = $crawler->filter('form[name=import_upload]')->form();
        $form['import_upload[fileFile]']->upload($path);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $importId = explode('/', $this->client->getRequest()->getPathInfo())[4];
        $this->client->submit($crawler->filter('form[name=import_mapping]')->form());
        $this->assertResponseRedirects();

        $this->executeImport($collection, $importId);

        $item = $this->findItems()['Frieren #1'];
        $this->assertSame('Abe Tsukasa', $item->getDatumByLabel('Author')?->getValue());
        $this->assertSame('2020', $item->getDatumByLabel('Year')?->getValue());
        $this->assertSame(DatumTypeEnum::TYPE_TEXT, $item->getDatumByLabel('Year')?->getType());
    }

    public function test_import_ignores_utf8_bom(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $path = tempnam(sys_get_temp_dir(), 'import') . '.csv';
        file_put_contents($path, "\xEF\xBB\xBF\"Name\",Author\nFrieren #1,Abe Tsukasa\n");

        $crawler = $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import');
        $form = $crawler->filter('form[name=import_upload]')->form();
        $form['import_upload[fileFile]']->upload($path);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertSame('Name', $crawler->filter('select[name="import_mapping[nameIndex]"] option[value="0"]')->text());
        $this->assertSame('0', $crawler->filter('form[name=import_mapping]')->form()->getValues()['import_mapping[nameIndex]']);
    }

    public function test_mapping_leaves_base_fields_empty_when_no_header_matches(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $path = tempnam(sys_get_temp_dir(), 'import') . '.csv';
        file_put_contents($path, "Title,,Picture\nFrieren #1,,\n");

        $crawler = $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import');
        $form = $crawler->filter('form[name=import_upload]')->form();
        $form['import_upload[fileFile]']->upload($path);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $values = $crawler->filter('form[name=import_mapping]')->form()->getValues();
        $this->assertSame('', $values['import_mapping[nameIndex]']);
        $this->assertSame('', $values['import_mapping[imageIndex]']);
        $this->assertSame('', $values['import_mapping[quantityIndex]']);
        $this->assertSame('', $values['import_mapping[tagsIndex]']);
        $this->assertSame('', $values['import_mapping[visibilityIndex]']);

        $this->assertCount(2, $crawler->filter('.mapping'));
        $this->assertSame('Title', $values['import_mapping[mapping][0][datumLabel]']);
        $this->assertSame('0', $values['import_mapping[mapping][0][columnIndex]']);
        $this->assertSame('Picture', $values['import_mapping[mapping][1][datumLabel]']);
        $this->assertSame('2', $values['import_mapping[mapping][1][columnIndex]']);
    }

    public function test_mapping_requires_name_column(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $path = tempnam(sys_get_temp_dir(), 'import') . '.csv';
        file_put_contents($path, "Title,Author\nFrieren #1,Abe Tsukasa\n");

        $crawler = $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import');
        $form = $crawler->filter('form[name=import_upload]')->form();
        $form['import_upload[fileFile]']->upload($path);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->client->submit($crawler->filter('form[name=import_mapping]')->form());

        $this->assertResponseStatusCodeSame(422);
    }

    public function test_import_does_not_read_local_files_as_image(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $importId = $this->uploadAndMap($collection, "name,image\nA,file:///etc/hostname\n", 0, 1, []);
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(0, $crawler->filter('tbody img'));

        $this->executeImport($collection, $importId);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $items = $this->findItems();
        $this->assertCount(1, $items);
        $this->assertNull($items['A']->getImage());
    }

    public function test_images_are_imported_from_archive(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $archive = $this->createArchive(['covers/frieren.png' => $this->createPng(10, 10)]);

        $importId = $this->uploadAndMap($collection, "name,image\nFrieren #1,covers/frieren.png\n", 0, 1, [], [], $archive);
        $imagePath = $this->client->getRequest()->getSession()->get('import_' . $importId)->getArchiveEntries()['covers/frieren.png']['path'];
        $this->assertStringStartsWith(sys_get_temp_dir() . '/koillection_import_', $imagePath);
        $this->assertFileExists($imagePath);

        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $src = $crawler->filter('tbody img')->attr('src');
        $this->assertStringContainsString('/import/' . $importId . '/image?name=covers', $src);

        $this->client->request(Request::METHOD_GET, $src);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'image/png');

        $this->executeImport($collection, $importId);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $this->assertNotNull($this->findItems()['Frieren #1']->getImage());
        $this->assertFileDoesNotExist($imagePath);
    }

    public function test_same_archive_image_can_be_used_by_several_rows(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $archive = $this->createArchive(['frieren.png' => $this->createPng(10, 10)]);

        $importId = $this->uploadAndMap($collection, "name,image\nFrieren #1,frieren.png\nFrieren #2,frieren.png\n", 0, 1, [], [], $archive);
        $this->executeImport($collection, $importId);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $items = $this->findItems();
        $this->assertNotNull($items['Frieren #1']->getImage());
        $this->assertNotNull($items['Frieren #2']->getImage());
        $this->assertNotSame($items['Frieren #1']->getImage(), $items['Frieren #2']->getImage());
    }

    public function test_archive_images_are_validated(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $archive = $this->createArchive([
            'frieren.png' => $this->createPng(10, 10),
            'script.png' => '<?php echo "Frieren";',
            'huge.png' => $this->createPng(8001, 1),
            'notes.txt' => 'Frieren',
        ]);

        $importId = $this->uploadAndMap($collection, "name,image\nA,missing.png\nB,../frieren.png\nC,/etc/hostname\nD,script.png\nE,huge.png\nF,notes.txt\n", 0, 1, [], [], $archive);
        $crawler = $this->client->followRedirect();

        $errors = $crawler->filter('.import-errors-table tbody tr td:last-child')->each(static fn ($td): string => $td->text());
        $this->assertSame([
            'Image not found in the archive',
            'Image not found in the archive',
            'Image not found in the archive',
            'Unsupported image type, use PNG, JPEG, WebP or AVIF',
            'Image dimensions are too large',
            'Unsupported image type, use PNG, JPEG, WebP or AVIF',
        ], $errors);

        $this->executeImport($collection, $importId);

        $this->assertResponseRedirects('/collections/' . $collection->getId() . '/import/' . $importId . '/preview');
        $this->assertCount(0, $this->findItems());
    }

    public function test_archive_image_is_required_when_value_is_not_an_url(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $this->uploadAndMap($collection, "name,image\nFrieren #1,frieren.png\n", 0, 1, []);
        $crawler = $this->client->followRedirect();

        $this->assertSame('Image not found in the archive', $crawler->filter('.import-errors-table tbody tr td:last-child')->text());
    }

    public function test_archive_decompression_is_limited(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $archive = $this->createArchive(['frieren.png' => str_repeat("\0", 5_000_000)]);

        $this->uploadAndMap($collection, "name,image\nFrieren #1,frieren.png\n", 0, 1, [], [], $archive);
        $crawler = $this->client->followRedirect();

        $this->assertSame('The archive contains more data than allowed', $crawler->filter('.import-errors-table tbody tr td:last-child')->text());
    }

    public function test_archive_image_route_returns_404_for_invalid_entry(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $archive = $this->createArchive(['frieren.png' => $this->createPng(10, 10)]);

        $importId = $this->uploadAndMap($collection, "name,image\nFrieren #1,frieren.png\n", 0, 1, [], [], $archive);
        $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import/' . $importId . '/image?name=../frieren.png');

        $this->assertResponseStatusCodeSame(404);
    }

    public function test_typed_values_are_normalized(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $importId = $this->uploadAndMap($collection, "name,Price,Country,Date,Owned,Rating\nA,4.5 eur,fr,2026-11-15,1,8\nB,,,,,\n", 0, null, [
            $this->mapping(1, DatumTypeEnum::TYPE_PRICE, 'Price', 1),
            $this->mapping(2, DatumTypeEnum::TYPE_COUNTRY, 'Country', 2),
            $this->mapping(3, DatumTypeEnum::TYPE_DATE, 'Date', 3),
            $this->mapping(4, DatumTypeEnum::TYPE_CHECKBOX, 'Owned', 4),
            $this->mapping(5, DatumTypeEnum::TYPE_RATING, 'Rating', 5),
        ]);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->executeImport($collection, $importId);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $items = $this->findItems();
        $this->assertSame('4.5', $items['A']->getDatumByLabel('Price')?->getValue());
        $this->assertSame('EUR', $items['A']->getDatumByLabel('Price')?->getCurrency());
        $this->assertSame('FR', $items['A']->getDatumByLabel('Country')?->getValue());
        $this->assertSame('2026-11-15', $items['A']->getDatumByLabel('Date')?->getValue());
        $this->assertNull($items['B']->getDatumByLabel('Price')?->getValue());
    }

    public function test_preview_lists_invalid_values_and_import_is_refused(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $importId = $this->uploadAndMap($collection, "name,Price,Country,Date,Owned,Rating,Year\nA,10,EN,15/11/2026,yes,11,abc\n,,,,,,\n", 0, null, [
            $this->mapping(1, DatumTypeEnum::TYPE_PRICE, 'Price', 1),
            $this->mapping(2, DatumTypeEnum::TYPE_COUNTRY, 'Country', 2),
            $this->mapping(3, DatumTypeEnum::TYPE_DATE, 'Date', 3),
            $this->mapping(4, DatumTypeEnum::TYPE_CHECKBOX, 'Owned', 4),
            $this->mapping(5, DatumTypeEnum::TYPE_RATING, 'Rating', 5),
            $this->mapping(6, DatumTypeEnum::TYPE_NUMBER, 'Year', 6),
        ]);
        $crawler = $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertCount(7, $crawler->filter('.import-errors-table tbody tr'));
        $this->assertCount(0, $crawler->filter('.items-table'));
        $this->assertStringNotContainsString('/import/' . $importId . '/import', (string) $this->client->getResponse()->getContent());

        $this->executeImport($collection, $importId);

        $this->assertResponseRedirects('/collections/' . $collection->getId() . '/import/' . $importId . '/preview');
        $this->assertCount(0, $this->findItems());
    }

    public function test_import_cannot_be_executed_twice(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $importId = $this->uploadAndMap($collection, "name\nA\nB\n", 0, null, []);
        $this->executeImport($collection, $importId);
        $this->executeImport($collection, $importId);

        $this->assertResponseStatusCodeSame(404);
        $this->assertCount(2, static::getContainer()->get('doctrine')->getRepository(Item::class)->findAll());
    }

    public function test_csv_is_stored_in_temp_dir_and_removed_after_import(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $importId = $this->uploadAndMap($collection, "name\nFrieren #1\n", 0, null, []);
        $path = $this->client->getRequest()->getSession()->get('import_' . $importId)->getPath();
        $this->assertStringStartsWith(sys_get_temp_dir() . '/koillection_import_', $path);
        $this->assertFileExists($path);

        $this->executeImport($collection, $importId);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $this->assertFileDoesNotExist($path);
        $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import/' . $importId . '/preview');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_dedicated_columns_are_imported(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);

        $importId = $this->uploadAndMap($collection, "name,Quantity,Tags,Visibility\nA,3,\"x, y\",Private\nB,,,\n", 0, null, [], [
            'quantityIndex' => 1,
            'tagsIndex' => 2,
            'visibilityIndex' => 3,
        ]);
        $this->executeImport($collection, $importId);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $items = $this->findItems();
        $this->assertSame(3, $items['A']->getQuantity());
        $this->assertSame(VisibilityEnum::VISIBILITY_PRIVATE, $items['A']->getVisibility());
        $this->assertCount(2, $items['A']->getTags());
        $this->assertSame(1, $items['B']->getQuantity());
        $this->assertSame(VisibilityEnum::VISIBILITY_INTERNAL, $items['B']->getVisibility());
        $this->assertCount(0, $items['B']->getTags());
    }

    public function test_invalid_quantity_and_visibility_are_reported(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $this->uploadAndMap($collection, "name,Quantity,Visibility\nA,0,secret\nB,2.5,public\n", 0, null, [], [
            'quantityIndex' => 1,
            'visibilityIndex' => 2,
        ]);
        $crawler = $this->client->followRedirect();

        $this->assertCount(3, $crawler->filter('.import-errors-table tbody tr'));
    }

    public function test_duplicates_are_unchecked_and_only_selected_rows_are_imported(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        ItemFactory::createOne(['name' => 'A', 'collection' => $collection, 'owner' => $user]);

        $importId = $this->uploadAndMap($collection, "name\nA\nB\nC\n", 0, null, []);
        $crawler = $this->client->followRedirect();

        $this->assertCount(1, $crawler->filter('tr.import-duplicate'));
        $this->assertCount(0, $crawler->filter('tr.import-duplicate input[checked]'));
        $this->assertCount(2, $crawler->filter('input[name="rows[]"][checked]'));

        $this->executeImport($collection, $importId, [1]);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $this->assertSame(['A', 'B'], $this->sortedNames());
    }

    public function test_import_without_selected_rows_is_refused(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $importId = $this->uploadAndMap($collection, "name\nA\n", 0, null, []);
        $this->executeImport($collection, $importId, []);

        $this->assertResponseRedirects('/collections/' . $collection->getId() . '/import/' . $importId . '/preview');
        $this->assertCount(0, $this->findItems());
    }

    public function test_cannot_access_import_of_another_user(): void
    {
        $owner = UserFactory::createOne();
        $otherUser = UserFactory::createOne();
        $this->client->loginUser($owner);
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $importId = $this->uploadAndMap($collection, "name\nA\n", 0, null, []);

        $this->client->loginUser($otherUser);
        $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import/' . $importId . '/preview');

        $this->assertResponseStatusCodeSame(404);
    }

    private function uploadAndMap(Collection $collection, string $csv, int $nameIndex, ?int $imageIndex, array $mapping, array $indexes = [], ?string $archive = null): string
    {
        $path = tempnam(sys_get_temp_dir(), 'import') . '.csv';
        file_put_contents($path, $csv);

        $crawler = $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import');
        $form = $crawler->filter('form[name=import_upload]')->form();
        $form['import_upload[fileFile]']->upload($path);
        if ($archive !== null) {
            $form['import_upload[archiveFile]']->upload($archive);
        }
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $importId = explode('/', $this->client->getRequest()->getPathInfo())[4];

        $form = $crawler->filter('form[name=import_mapping]')->form();
        $values = $form->getPhpValues();
        $values['import_mapping']['nameIndex'] = $nameIndex;
        $values['import_mapping']['imageIndex'] = $imageIndex;
        foreach ($indexes as $field => $index) {
            $values['import_mapping'][$field] = $index;
        }
        $values['import_mapping']['mapping'] = $mapping;
        $this->client->request($form->getMethod(), $form->getUri(), $values);
        $this->assertResponseRedirects();

        return $importId;
    }

    private function createArchive(array $entries): string
    {
        $path = tempnam(sys_get_temp_dir(), 'import') . '.zip';
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE);
        foreach ($entries as $name => $content) {
            $zip->addFromString($name, $content);
        }
        $zip->close();

        return $path;
    }

    private function createPng(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        ob_start();
        imagepng($image);

        return ob_get_clean();
    }

    private function mapping(int $columnIndex, string $type, string $label, int $position, bool $createTags = false): array
    {
        $mapping = [
            'columnIndex' => $columnIndex,
            'datumType' => $type,
            'datumLabel' => $label,
            'datumVisibility' => VisibilityEnum::VISIBILITY_PUBLIC,
            'datumPosition' => $position,
        ];

        if ($createTags) {
            $mapping['createCorrespondingTags'] = '1';
        }

        return $mapping;
    }

    private function executeImport(Collection $collection, string $importId, ?array $rows = null): void
    {
        if ($rows === null) {
            $crawler = $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import/' . $importId . '/preview');
            $rows = $crawler->filter('input[name="rows[]"]')->each(static fn ($input): string => $input->attr('value'));
        }

        $this->client->request(Request::METHOD_POST, $this->importUrl($collection, $importId), [
            'rows' => $rows,
        ]);
    }

    private function importUrl(Collection $collection, string $importId): string
    {
        return '/collections/' . $collection->getId() . '/import/' . $importId . '/import';
    }

    private function sortedNames(): array
    {
        $names = array_keys($this->findItems());
        sort($names);

        return $names;
    }

    private function findItems(): array
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->clear();

        $items = [];
        foreach ($em->getRepository(Item::class)->findAll() as $item) {
            $items[$item->getName()] = $item;
        }

        return $items;
    }
}
