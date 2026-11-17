<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Enum\DatumTypeEnum;
use App\Enum\ScraperTypeEnum;
use App\Model\ScrapingCollection;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NoPrivateNetworkHttpClient;

/**
 * @extends HtmlScraper<ScrapingCollection>
 */
class HtmlCollectionScraper extends HtmlScraper
{
    #[\Override]
    public function scrap($scraping): array
    {
        $crawler = $this->getCrawler($scraping);
        $scraper = $scraping->getScraper();

        $image = $scraping->getScrapImage() ? $this->extract($scraper->getImagePath(), DatumTypeEnum::TYPE_TEXT, $crawler, $scraping) : null;
        $image = $this->guessHost($image, $scraping);

        $base64Image = null;
        if ($image !== null) {
            $response = new NoPrivateNetworkHttpClient(new CurlHttpClient())->request('GET', $image, ['timeout' => 2.5]);
            $contentType = $response->getHeaders()['content-type'][0] ?? 'application/octet-stream';
            $base64Image = 'data:' . $contentType . ';base64,' . base64_encode($response->getContent());
        }

        return [
            'name' => $scraping->getScrapName() ? $this->extract($scraper->getNamePath(), DatumTypeEnum::TYPE_TEXT, $crawler, $scraping) : null,
            'base64Image' => $base64Image,
            'data' => $this->scrapData($scraping, $crawler, ScraperTypeEnum::TYPE_COLLECTION),
            'scrapedUrl' => $scraping->getUrl()
        ];
    }
}
