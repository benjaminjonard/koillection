<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Enum\ScraperContentTypeEnum;
use App\Model\ScrapingItem;

/**
 * @implements Scraper<ScrapingItem>
 */
class ItemScraper implements Scraper
{
    private array $scrapers;

    public function __construct(
        HtmlItemScraper $htmlItemScraper,
        JsonItemScraper $jsonItemScraper,
    ) {
        $this->scrapers = [
            ScraperContentTypeEnum::CONTENT_TYPE_HTML => $htmlItemScraper,
            ScraperContentTypeEnum::CONTENT_TYPE_JSON => $jsonItemScraper,
        ];
    }

    #[\Override]
    public function scrap($scraping): array
    {
        return $this->scrapers[$scraping->getScraper()->getContentType()]->scrap($scraping);
    }
}
