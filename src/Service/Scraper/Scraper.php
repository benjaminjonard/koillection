<?php

declare(strict_types=1);

namespace App\Service\Scraper;

/**
 * @template ScrapedType
 */
interface Scraper
{
    /**
     * @param ScrapedType $scraping
     */
    public function scrap($scraping): array;
}
