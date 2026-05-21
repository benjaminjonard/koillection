<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Enum\ScraperContentTypeEnum;
use App\Model\ScrapingWish;
use App\Service\Scraper\Scraper;
use Twig\Environment;

/**
 * @implements Scraper<ScrapingWish>
 */
class WishScraper implements Scraper {
    private array $scrapers;

    public function __construct(Environment $twig)
    {
        $this->scrapers = [
            ScraperContentTypeEnum::CONTENT_TYPE_HTML => new HtmlWishScraper($twig),
            ScraperContentTypeEnum::CONTENT_TYPE_JSON => new JsonWishScraper($twig),
        ];
    }

    #[\Override]
    public function scrap($scraping): array
    {
        return $this->scrapers[$scraping->getScraper()->getContentType()]->scrap($scraping);
    }
}