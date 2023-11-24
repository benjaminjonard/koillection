<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Enum\DatumTypeEnum;
use App\Model\ScrapingWish;

class HtmlWishScraper extends HtmlScraper
{
    public function scrap(ScrapingWish $scraping): array
    {
        $crawler = $this->getCrawler($scraping);
        $scraper = $scraping->getScraper();

        $image = $scraping->getScrapImage() ? $this->extract($scraper->getImagePath(), DatumTypeEnum::TYPE_TEXT, $crawler) : null;
        $image = $this->guessHost($image, $scraping);

        $price = $scraping->getScrapName() ? $this->extract($scraper->getNamePath(), DatumTypeEnum::TYPE_TEXT, $crawler) : null;

        return [
            'name' => $scraping->getScrapName() ? $this->extract($scraper->getNamePath(), DatumTypeEnum::TYPE_TEXT, $crawler) : null,
            'image' => $image,
            'price' => $price,
            'scrapedUrl' => $scraping->getUrl()
        ];
    }
}