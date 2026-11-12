<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Model\ScrapingCollection;
use App\Model\ScrapingItem;
use App\Model\ScrapingWish;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NoPrivateNetworkHttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Twig\Environment;

abstract class HtmlScraper extends ContentScraper
{
    public function __construct(
        Environment $twig,
        private readonly ScrapingUrlGuard $urlGuard,
        private readonly ScrapingBrowser $browser,
    ) {
        parent::__construct($twig);
    }

    protected function getCrawler(ScrapingItem|ScrapingCollection|ScrapingWish $scraping): Crawler
    {
        if ($scraping->getFile() instanceof UploadedFile) {
            return new Crawler($scraping->getFile()->getContent());
        }

        $this->urlGuard->assertSchemeIsSupported($scraping->getUrl());

        $html = $this->browser->isAvailable()
            ? $this->fetchWithBrowser($scraping)
            : $this->fetchWithHttpClient($scraping);

        // Strip non-visible elements so XPath cannot match RSC payloads, React hydration
        // templates, or inline scripts — PHP's DOMDocument does not honour HTML5 semantics
        // for <script> and <template> and would otherwise include their content in the tree.
        $html = preg_replace('@<(script|template|noscript)[^>]*>.*?</\1>@si', '', $html);

        return new Crawler($html);
    }

    private function fetchWithBrowser(ScrapingItem|ScrapingCollection|ScrapingWish $scraping): string
    {
        $this->urlGuard->resolvePublicAddress($scraping->getUrl());

        return $this->browser->fetch($scraping->getUrl(), $this->getCustomHeaders($scraping));
    }

    private function fetchWithHttpClient(ScrapingItem|ScrapingCollection|ScrapingWish $scraping): string
    {
        $headers = ['Accept-Encoding' => 'gzip', ...$this->getCustomHeaders($scraping)];

        $response = new NoPrivateNetworkHttpClient(new CurlHttpClient())->request('GET', $scraping->getUrl(), [
            'headers' => $headers,
            'extra' => ['curl' => [\CURLOPT_ENCODING => '']],
        ]);

        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException('Scraping error: ' . $response->getStatusCode());
        }

        return $response->getContent();
    }

    private function getCustomHeaders(ScrapingItem|ScrapingCollection|ScrapingWish $scraping): array
    {
        $headers = [];
        foreach ($scraping->getScraper()->getHeaders() ?? [] as $header) {
            $headers[$header['header']] = $header['value'];
        }

        return $headers;
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
