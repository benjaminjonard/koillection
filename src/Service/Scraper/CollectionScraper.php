<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Enum\ScraperContentTypeEnum;
use App\Model\ScrapingCollection;

/**
 * @implements Scraper<ScrapingCollection>
 */
class CollectionScraper implements Scraper
{
    private array $scrapers;

    public function __construct(
        HtmlCollectionScraper $htmlCollectionScraper,
        JsonCollectionScraper $jsonCollectionScraper,
    ) {
        $this->scrapers = [
            ScraperContentTypeEnum::CONTENT_TYPE_HTML => $htmlCollectionScraper,
            ScraperContentTypeEnum::CONTENT_TYPE_JSON => $jsonCollectionScraper,
        ];
    }

    #[\Override]
    public function scrap($scraping): array
    {
        return $this->scrapers[$scraping->getScraper()->getContentType()]->scrap($scraping);
    }
}
