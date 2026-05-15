<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Enum\DatumTypeEnum;
use App\Enum\ScraperTypeEnum;
use App\Model\ScrapingCollection;

/**
 * @extends JsonScraper<ScrapingCollection>
 */
class JsonCollectionScraper extends JsonScraper
{
    #[\Override]
    public function scrap($scraping): array
    {
        $crawler = $this->getCrawler($scraping);
        $scraper = $scraping->getScraper();

        $image = $scraping->getScrapImage() ? $this->extract($scraper->getImagePath(), DatumTypeEnum::TYPE_TEXT, $crawler, $scraping) : null;
        $image = $this->guessHost($image, $scraping);

        $response = $this->client->request(
            'GET',
            $image,
            ['timeout' => 2.5]
        );

        return [
            'name' => $scraping->getScrapName() ? $this->extract($scraper->getNamePath(), DatumTypeEnum::TYPE_TEXT, $crawler, $scraping) : null,
            'base64Image' => 'data:' . $response->getHeaders()['content-type'][0] . ';base64,' . base64_encode($response->getContent()),
            'data' => $this->scrapData($scraping, $crawler, ScraperTypeEnum::TYPE_ITEM),
            'scrapedUrl' => $scraping->getUrl()
        ];
    }
}
