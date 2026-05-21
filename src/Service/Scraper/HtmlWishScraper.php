<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Enum\DatumTypeEnum;
use App\Model\ScrapingWish;

/**
 * @extends HtmlScraper<ScrapingWish>
 */
class HtmlWishScraper extends HtmlScraper
{
    #[\Override]
    public function scrap($scraping): array
    {
        $crawler = $this->getCrawler($scraping);
        $scraper = $scraping->getScraper();

        $image = $scraping->getScrapImage() ? $this->extract($scraper->getImagePath(), DatumTypeEnum::TYPE_TEXT, $crawler, $scraping) : null;
        $image = $this->guessHost($image, $scraping);

        $price = $scraping->getScrapPrice() ? $this->extract($scraper->getPricePath(), DatumTypeEnum::TYPE_TEXT, $crawler, $scraping) : null;
        if ($price) {
            $price = preg_replace('/[^0-9-.,]+/ ', '', $price);
        }

        return [
            'name' => $scraping->getScrapName() ? $this->extract($scraper->getNamePath(), DatumTypeEnum::TYPE_TEXT, $crawler, $scraping) : null,
            'image' => $image,
            'price' => $price,
            'scrapedUrl' => $scraping->getUrl()
        ];
    }
}
