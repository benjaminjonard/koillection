<?php

declare(strict_types=1);

namespace App\Tests\App\Import;

use App\Entity\Collection;
use App\Entity\Import;
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

        $import = $this->uploadAndMap($collection, "name,Author,Year\nFrieren #1,Abe Tsukasa,2020\nDune,Frank Herbert,1965\n", 0, null, [
            $this->mapping(1, DatumTypeEnum::TYPE_TEXT, 'Author', 1),
            $this->mapping(2, DatumTypeEnum::TYPE_NUMBER, 'Year', 2),
        ]);
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(2, $crawler->filter('tbody tr'));

        $this->executeImport($collection, $import);

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

        $import = $this->uploadAndMap($collection, "name,Author\nA,\"New tag, Other tag\"\nB,New tag\n", 0, null, [
            $this->mapping(1, DatumTypeEnum::TYPE_TEXT, 'Author', 1, true),
        ]);
        $this->executeImport($collection, $import);

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

    public function test_import_does_not_read_local_files_as_image(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $import = $this->uploadAndMap($collection, "name,image\nA,/etc/hostname\nB,file:///etc/hostname\n", 0, 1, []);
        $crawler = $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertCount(0, $crawler->filter('tbody img'));

        $this->executeImport($collection, $import);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $items = $this->findItems();
        $this->assertCount(2, $items);
        $this->assertNull($items['A']->getImage());
        $this->assertNull($items['B']->getImage());
    }

    public function test_typed_values_are_normalized(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $import = $this->uploadAndMap($collection, "name,Price,Country,Date,Owned,Rating\nA,4.5 eur,fr,2026-11-15,1,8\nB,,,,,\n", 0, null, [
            $this->mapping(1, DatumTypeEnum::TYPE_PRICE, 'Price', 1),
            $this->mapping(2, DatumTypeEnum::TYPE_COUNTRY, 'Country', 2),
            $this->mapping(3, DatumTypeEnum::TYPE_DATE, 'Date', 3),
            $this->mapping(4, DatumTypeEnum::TYPE_CHECKBOX, 'Owned', 4),
            $this->mapping(5, DatumTypeEnum::TYPE_RATING, 'Rating', 5),
        ]);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        $this->executeImport($collection, $import);

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

        $import = $this->uploadAndMap($collection, "name,Price,Country,Date,Owned,Rating,Year\nA,10,EN,15/11/2026,yes,11,abc\n,,,,,,\n", 0, null, [
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
        $this->assertStringNotContainsString('/import/' . $import->getId() . '/import', (string) $this->client->getResponse()->getContent());

        $this->executeImport($collection, $import);

        $this->assertResponseRedirects('/collections/' . $collection->getId() . '/import/' . $import->getId() . '/preview');
        $this->assertCount(0, $this->findItems());
    }

    public function test_import_cannot_be_executed_twice(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $import = $this->uploadAndMap($collection, "name\nA\nB\n", 0, null, []);
        $this->executeImport($collection, $import);
        $this->executeImport($collection, $import);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $this->assertCount(2, static::getContainer()->get('doctrine')->getRepository(Item::class)->findAll());
    }

    public function test_dedicated_columns_are_imported(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user, 'visibility' => VisibilityEnum::VISIBILITY_INTERNAL]);

        $import = $this->uploadAndMap($collection, "name,Quantity,Tags,Visibility\nA,3,\"x, y\",Private\nB,,,\n", 0, null, [], [
            'quantityIndex' => 1,
            'tagsIndex' => 2,
            'visibilityIndex' => 3,
        ]);
        $this->executeImport($collection, $import);

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

        $import = $this->uploadAndMap($collection, "name\nA\nB\nC\n", 0, null, []);
        $crawler = $this->client->followRedirect();

        $this->assertCount(1, $crawler->filter('tr.import-duplicate'));
        $this->assertCount(0, $crawler->filter('tr.import-duplicate input[checked]'));
        $this->assertCount(2, $crawler->filter('input[name="rows[]"][checked]'));

        $this->executeImport($collection, $import, [1]);

        $this->assertResponseRedirects('/collections/' . $collection->getId());
        $this->assertSame(['A', 'B'], $this->sortedNames());
        $em = static::getContainer()->get('doctrine')->getManager();
        $import = $em->getRepository(Import::class)->find($import->getId());
        $this->assertSame(3, $import->getTotalNumberOfItems());
        $this->assertSame(1, $import->getNumberOfImportedItems());
        $this->assertSame(2, $import->getNumberOfSkippedItems());
        $this->assertSame(1, $import->getNumberOfDuplicatedItems());
    }

    public function test_import_without_selected_rows_is_refused(): void
    {
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);

        $import = $this->uploadAndMap($collection, "name\nA\n", 0, null, []);
        $this->executeImport($collection, $import, []);

        $this->assertResponseRedirects('/collections/' . $collection->getId() . '/import/' . $import->getId() . '/preview');
        $this->assertCount(0, $this->findItems());
    }

    public function test_cannot_access_import_of_another_user(): void
    {
        $owner = UserFactory::createOne();
        $this->client->loginUser($owner);
        $collection = CollectionFactory::createOne(['owner' => $owner]);
        $import = $this->uploadAndMap($collection, "name\nA\n", 0, null, []);

        $this->client->loginUser(UserFactory::createOne());
        $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import/' . $import->getId() . '/preview');

        $this->assertResponseStatusCodeSame(404);
    }

    private function uploadAndMap(Collection $collection, string $csv, int $nameIndex, ?int $imageIndex, array $mapping, array $indexes = []): Import
    {
        $path = tempnam(sys_get_temp_dir(), 'import') . '.csv';
        file_put_contents($path, $csv);

        $crawler = $this->client->request(Request::METHOD_GET, '/collections/' . $collection->getId() . '/import');
        $form = $crawler->filter('form[name=import_upload]')->form();
        $form['import_upload[fileFile]']->upload($path);
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

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

        $em = static::getContainer()->get('doctrine')->getManager();
        $em->clear();

        return $em->getRepository(Import::class)->findOneBy([], ['createdAt' => 'DESC']);
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

    private function executeImport(Collection $collection, Import $import, ?array $rows = null): void
    {
        $this->client->request(Request::METHOD_POST, $this->importUrl($collection, $import), [
            'rows' => $rows ?? array_keys($import->getRows()),
        ]);
    }

    private function importUrl(Collection $collection, Import $import): string
    {
        return '/collections/' . $collection->getId() . '/import/' . $import->getId() . '/import';
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
