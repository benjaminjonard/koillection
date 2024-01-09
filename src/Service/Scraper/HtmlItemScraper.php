<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Enum\DatumTypeEnum;
use App\Enum\ScraperTypeEnum;
use App\Model\ScrapingItem;

class HtmlItemScraper extends HtmlScraper
{
    public function scrap(ScrapingItem $scraping): array
    {
        $crawler = $this->getCrawler($scraping);
        $scraper = $scraping->getScraper();

        $image = $scraping->getScrapImage() ? $this->extract($scraper->getImagePath(), DatumTypeEnum::TYPE_TEXT, $crawler) : null;
        $image = $this->guessHost($image, $scraping);

        return [
            'name' => $scraping->getScrapName() ? $this->extract($scraper->getNamePath(), DatumTypeEnum::TYPE_TEXT, $crawler) : null,
            'image' => $image,
            'data' => $this->scrapData($scraping, $crawler, ScraperTypeEnum::TYPE_ITEM),
            'scrapedUrl' => $scraping->getUrl()
        ];
    }
}
