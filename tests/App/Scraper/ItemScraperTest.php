<?php

declare(strict_types=1);

namespace App\Tests\App\Scraper;

use App\Enum\DatumTypeEnum;
use App\Enum\ScraperTypeEnum;
use App\Tests\AppTestCase;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\PathFactory;
use App\Tests\Factory\ScraperFactory;
use App\Tests\Factory\UserFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ItemScraperTest extends AppTestCase
{
    use Factories;
    use ResetDatabase;

    private KernelBrowser $client;

    #[\Override]
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function test_can_see_item_scraper_list(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/scrapers/item-scrapers');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Scrapers', $crawler->filter('h1')->text());
    }

    public function test_can_get_item_scraper(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $scraper = ScraperFactory::createOne(['type' => ScraperTypeEnum::TYPE_ITEM, 'owner' => $user]);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/scrapers/item-scrapers/' . $scraper->getId());

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame($scraper->getName(), $crawler->filter('h1')->text());
    }

    public function test_can_add_item_scraper(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        // Act
        $this->client->request(Request::METHOD_GET, '/scrapers/item-scrapers/add');

        $crawler = $this->client->submitForm('Submit', [
            'scraper[name]' => 'Manga News'
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Manga News', $crawler->filter('h1')->text());
    }

    public function test_can_edit_item_scraper(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $scraper = ScraperFactory::createOne(['type' => ScraperTypeEnum::TYPE_ITEM, 'owner' => $user]);
        PathFactory::createOne(['scraper' => $scraper, 'owner' => $user]);

        // Act
        $this->client->request(Request::METHOD_GET, '/scrapers/item-scrapers/' . $scraper->getId() . '/edit');
        $crawler = $this->client->submitForm('Submit', [
            'scraper[name]' => 'Manga News',
            'scraper[namePath]' => '//h1/text()',
            'scraper[imagePath]' => '//h1/img/@src',
            'scraper[dataPaths][0][name]' => 'Author',
            'scraper[dataPaths][0][type]' => DatumTypeEnum::TYPE_TEXT,
            'scraper[dataPaths][0][path]' => '//h2/text()',
            'scraper[dataPaths][0][position]' => '1'
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSame('Manga News', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('tbody tr'));
    }

    public function test_can_delete_item_scraper(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $scraper = ScraperFactory::createOne(['type' => ScraperTypeEnum::TYPE_ITEM, 'owner' => $user]);
        PathFactory::createOne(['scraper' => $scraper, 'owner' => $user]);

        // Act
        $crawler = $this->client->request(Request::METHOD_GET, '/scrapers/item-scrapers/' . $scraper->getId());
        $crawler->filter('#modal-delete form')->getNode(0)->setAttribute('action', '/scrapers/item-scrapers/' . $scraper->getId() . '/delete');
        $this->client->submitForm('OK');

        // Assert
        $this->assertResponseIsSuccessful();
        ScraperFactory::assert()->count(0);
        PathFactory::assert()->count(0);
    }

    public function test_can_export_item_scraper(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $scraper = ScraperFactory::createOne(['type' => ScraperTypeEnum::TYPE_ITEM, 'owner' => $user]);
        PathFactory::createOne(['scraper' => $scraper, 'owner' => $user]);

        // Act
        $this->client->request(Request::METHOD_GET, '/scrapers/item-scrapers/' . $scraper->getId() . '/export');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }

    public function test_can_import_item_scraper(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);

        // Act
        $this->client->request(Request::METHOD_GET, '/scrapers/item-scrapers');

        $crawler = $this->client->submitForm('Import', [
            'item_scraper_importer[file]' => $this->createFile('json'),
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        ScraperFactory::assert()->count(1);
        $this->assertSame('Nyancat', $crawler->filter('h1')->text());
        $this->assertCount(3, $crawler->filter('tbody tr'));

        $this->assertSame('Name path', $crawler->filter('.list-element')->eq(0)->filter('td')->eq(0)->text());
        $this->assertSame('#//h1/text()]#', $crawler->filter('.list-element')->eq(0)->filter('td')->eq(1)->text());
        $this->assertSame('Image path', $crawler->filter('.list-element')->eq(1)->filter('td')->eq(0)->text());
        $this->assertSame('#//h1/img/@src]#', $crawler->filter('.list-element')->eq(1)->filter('td')->eq(1)->text());
        $this->assertSame('Length', $crawler->filter('.list-element')->eq(2)->filter('td')->eq(0)->text());
        $this->assertSame('#//span/text()]#', $crawler->filter('.list-element')->eq(2)->filter('td')->eq(1)->text());
    }

    public function test_scraped_data_are_grouped_by_holder(): void
    {
        // Arrange
        $user = UserFactory::createOne();
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $scraper = ScraperFactory::createOne(['type' => ScraperTypeEnum::TYPE_ITEM, 'owner' => $user]);
        $listPath = PathFactory::createOne(['scraper' => $scraper, 'owner' => $user, 'name' => 'Platforms', 'type' => DatumTypeEnum::TYPE_LIST, 'path' => '#//li/text()#', 'position' => 1]);
        $imagePath = PathFactory::createOne(['scraper' => $scraper, 'owner' => $user, 'name' => 'Cover', 'type' => DatumTypeEnum::TYPE_IMAGE, 'path' => '#//img/@src#', 'position' => 2]);

        $htmlPath = '/tmp/' . uniqid() . '.html';
        file_put_contents($htmlPath, '<html><body><ul><li>Amiga</li><li>Atari ST</li></ul><img src="https://example.com/cover.png"></body></html>');

        $crawler = $this->client->request(Request::METHOD_GET, '/items/add?collection=' . $collection->getId());
        $token = $crawler->filter('input[name="scraping_item[_token]"]')->attr('value');

        // Act
        $this->client->request(Request::METHOD_POST, '/scrapers/item-scrapers/scrap', [
            'scraping_item' => [
                'scraper' => $scraper->getId(),
                'dataToScrap' => [$listPath->getId(), $imagePath->getId()],
                '_token' => $token,
            ],
        ], [
            'scraping_item' => ['file' => new UploadedFile($htmlPath, 'page.html', 'text/html', test: true)],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true)['data'];
        $this->assertSame(['text', 'Platforms'], \array_slice($data[0], 0, 2));
        $this->assertSame(['image', 'Cover'], \array_slice($data[1], 0, 2));
    }

    public static function scrapedCountries(): iterable
    {
        yield ['Germany'];
        yield [' germany '];
        yield ['Allemagne'];
        yield ['DE'];
        yield ['DEU'];
    }

    #[DataProvider('scrapedCountries')]
    public function test_scraped_country_is_converted_to_country_code(string $scrapedCountry): void
    {
        // Arrange
        $user = UserFactory::createOne(['locale' => 'fr']);
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $scraper = ScraperFactory::createOne(['type' => ScraperTypeEnum::TYPE_ITEM, 'owner' => $user]);
        $countryPath = PathFactory::createOne(['scraper' => $scraper, 'owner' => $user, 'name' => 'Country', 'type' => DatumTypeEnum::TYPE_COUNTRY, 'path' => '#//em/text()#', 'position' => 1]);

        $htmlPath = '/tmp/' . uniqid() . '.html';
        file_put_contents($htmlPath, "<html><body><em>{$scrapedCountry}</em></body></html>");

        $crawler = $this->client->request(Request::METHOD_GET, '/items/add?collection=' . $collection->getId());
        $token = $crawler->filter('input[name="scraping_item[_token]"]')->attr('value');

        // Act
        $this->client->request(Request::METHOD_POST, '/scrapers/item-scrapers/scrap', [
            'scraping_item' => [
                'scraper' => $scraper->getId(),
                'dataToScrap' => [$countryPath->getId()],
                '_token' => $token,
            ],
        ], [
            'scraping_item' => ['file' => new UploadedFile($htmlPath, 'page.html', 'text/html', test: true)],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true)['data'];
        $this->assertMatchesRegularExpression('/value="DE"[^>]*selected/', $data[0][2]);
    }

    public static function scrapedPrices(): iterable
    {
        yield ['19,99 €', '19.99'];
        yield ['$1,299.00', '1299.00'];
        yield ['1.234,56 EUR', '1234.56'];
        yield ['1 299,5 €', '1299.5'];
        yield ['15 €', '15'];
    }

    #[DataProvider('scrapedPrices')]
    public function test_scraped_price_is_converted_to_number(string $scrapedPrice, string $expectedValue): void
    {
        // Arrange
        $user = UserFactory::createOne(['currency' => 'EUR']);
        $this->client->loginUser($user);
        $collection = CollectionFactory::createOne(['owner' => $user]);
        $scraper = ScraperFactory::createOne(['type' => ScraperTypeEnum::TYPE_ITEM, 'owner' => $user]);
        $pricePath = PathFactory::createOne(['scraper' => $scraper, 'owner' => $user, 'name' => 'Price', 'type' => DatumTypeEnum::TYPE_PRICE, 'path' => '#//em/text()#', 'position' => 1]);

        $htmlPath = '/tmp/' . uniqid() . '.html';
        file_put_contents($htmlPath, "<html><body><em>{$scrapedPrice}</em></body></html>");

        $crawler = $this->client->request(Request::METHOD_GET, '/items/add?collection=' . $collection->getId());
        $token = $crawler->filter('input[name="scraping_item[_token]"]')->attr('value');

        // Act
        $this->client->request(Request::METHOD_POST, '/scrapers/item-scrapers/scrap', [
            'scraping_item' => [
                'scraper' => $scraper->getId(),
                'dataToScrap' => [$pricePath->getId()],
                '_token' => $token,
            ],
        ], [
            'scraping_item' => ['file' => new UploadedFile($htmlPath, 'page.html', 'text/html', test: true)],
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $data = json_decode($this->client->getResponse()->getContent(), true)['data'];
        $this->assertSame(['text', 'Price'], \array_slice($data[0], 0, 2));
        $this->assertMatchesRegularExpression('/type="number"[^>]*value="' . preg_quote($expectedValue, '/') . '"/', $data[0][2]);
        $this->assertMatchesRegularExpression('/value="EUR"\s*selected/', $data[0][2]);
    }
}
