<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Model\ScrapingCollection;
use App\Model\ScrapingItem;
use App\Model\ScrapingWish;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Panther\Client as PantherClient;

/**
 * @template ScrapedType
 *
 * @extends ContentScraper<ScrapedType, Crawler>
 */
abstract class HtmlScraper extends ContentScraper
{
    protected function getCrawler(ScrapingItem|ScrapingCollection|ScrapingWish $scraping): Crawler
    {
        if ($scraping->getFile() instanceof UploadedFile) {
            return new Crawler($scraping->getFile()->getContent());
        }

        $pantherClient = PantherClient::createChromeClient(null, [
            '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--window-size=1200,1100',
        ], ['port' => random_int(9515, 9999)]);

        try {
            $pantherClient->request('GET', $scraping->getUrl());

            // Poll until the DOM stops changing — required for SPAs (Next.js RSC, React, etc.)
            // getPageSource() called immediately after request() captures pre-hydration HTML
            $html = '';
            $previousHtml = null;
            $deadline = time() + 10;
            do {
                usleep(500_000);
                $html = $pantherClient->executeScript('return document.documentElement.outerHTML');
                if ($html === $previousHtml) {
                    break;
                }
                $previousHtml = $html;
            } while (time() < $deadline);
        } finally {
            $pantherClient->quit();
        }

        // Strip non-visible elements so XPath cannot match RSC payloads, React hydration
        // templates, or inline scripts — PHP's DOMDocument does not honour HTML5 semantics
        // for <script> and <template> and would otherwise include their content in the tree.
        $html = preg_replace('@<(script|template|noscript)[^>]*>.*?</\1>@si', '', $html);

        return new Crawler($html);
    }

    #[\Override]
    protected function extract(?string $template, string $type, $crawler, $scraping): ?string
    {
        if (!$template) {
            return '';
        }

        $values = [];
        preg_match_all('/#(.*?)#/', $template, $matches);

        foreach ($matches[1] as $xPath) {
            $results = $crawler->evaluate($xPath);

            if ($results instanceof Crawler) {
                $results = $results->each(static function (Crawler $node): string {
                    $domNode = $node->getNode(0);
                    if ($domNode instanceof \DOMAttr) {
                        return $domNode->value;
                    }

                    return $node->text();
                });
            }

            foreach ($results as $key => $result) {
                if (isset($values[$key])) {
                    $values[$key] = str_replace("#{$xPath}#", $result, $values[$key]);
                } else {
                    $values[$key] = str_replace("#{$xPath}#", $result, $template);
                }
            }

            // Remove xPath from result in case nothing was found
            foreach ($values as &$value) {
                $value = str_replace("#{$xPath}#", '', $value);
            }
        }

        return $this->formatValues($values, $type, $scraping);
    }
}
