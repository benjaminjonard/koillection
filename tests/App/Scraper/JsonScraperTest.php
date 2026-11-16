<?php

declare(strict_types=1);

namespace App\Tests\App\Scraper;

use App\Entity\Path;
use App\Entity\Scraper;
use App\Enum\DatumTypeEnum;
use App\Enum\ScraperContentTypeEnum;
use App\Enum\ScraperTypeEnum;
use App\Model\ScrapingItem;
use App\Service\Scraper\JsonItemScraper;
use App\Service\Scraper\ScrapingUrlGuard;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Twig\Environment;

class JsonScraperTest extends KernelTestCase
{
    public function test_json_api_is_scraped_without_forcing_accept_encoding(): void
    {
        // Arrange
        $requestHeaders = [];
        $client = new MockHttpClient(static function (string $method, string $url, array $options) use (&$requestHeaders): MockResponse {
            $requestHeaders = $options['headers'];

            return new MockResponse('{"title":"Never Gonna Give You Up","year":1987}', ['response_headers' => ['Content-Type' => 'application/json']]);
        });
        $scraper = new JsonItemScraper(self::getContainer()->get(Environment::class), $client, new ScrapingUrlGuard());

        $scraperEntity = new Scraper()
            ->setName('API')
            ->setType(ScraperTypeEnum::TYPE_ITEM)
            ->setContentType(ScraperContentTypeEnum::CONTENT_TYPE_JSON)
            ->setNamePath('#title#');
        $yearPath = new Path()->setName('Year')->setPath('#year#')->setType(DatumTypeEnum::TYPE_TEXT)->setPosition(1)->setScraper($scraperEntity);

        $scraping = new ScrapingItem()
            ->setUrl('https://1.1.1.1/releases/249504')
            ->setScraper($scraperEntity)
            ->setScrapName(true)
            ->setDataToScrap([$yearPath]);

        // Act
        $result = $scraper->scrap($scraping);

        // Assert
        $this->assertSame('Never Gonna Give You Up', $result['name']);
        $this->assertStringContainsString('value="1987"', $result['data'][0][2]);
        $this->assertContains('Accept: application/json', $requestHeaders);
        $this->assertSame([], preg_grep('/^Accept-Encoding:/i', $requestHeaders));
    }
}
