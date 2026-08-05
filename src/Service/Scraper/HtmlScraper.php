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
use Symfony\Component\Panther\Client as PantherClient;
use Twig\Environment;

abstract class HtmlScraper extends ContentScraper
{
    public function __construct(
        Environment $twig,
        private readonly ScrapingUrlGuard $urlGuard,
    ) {
        parent::__construct($twig);
    }

    protected function getCrawler(ScrapingItem|ScrapingCollection|ScrapingWish $scraping): Crawler
    {
        if ($scraping->getFile() instanceof UploadedFile) {
            return new Crawler($scraping->getFile()->getContent());
        }

        $this->urlGuard->assertSchemeIsSupported($scraping->getUrl());

        $html = $this->isPantherAvailable()
            ? $this->fetchWithPanther($scraping)
            : $this->fetchWithHttpClient($scraping);

        // Strip non-visible elements so XPath cannot match RSC payloads, React hydration
        // templates, or inline scripts — PHP's DOMDocument does not honour HTML5 semantics
        // for <script> and <template> and would otherwise include their content in the tree.
        $html = preg_replace('@<(script|template|noscript)[^>]*>.*?</\1>@si', '', $html);

        return new Crawler($html);
    }

    private function fetchWithPanther(ScrapingItem|ScrapingCollection|ScrapingWish $scraping): string
    {
        $url = $scraping->getUrl();
        $host = trim((string) parse_url($url, \PHP_URL_HOST), '[]');
        $address = $this->urlGuard->resolvePublicAddress($url);

        $arguments = [
            '--headless',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--disable-gpu',
            '--window-size=1200,1100',
        ];

        if (!filter_var($host, \FILTER_VALIDATE_IP)) {
            $mapped = str_contains($address, ':') ? "[{$address}]" : $address;
            $arguments[] = "--host-resolver-rules=MAP {$host} {$mapped}";
        }

        $pantherClient = PantherClient::createChromeClient(null, $arguments, ['port' => random_int(9515, 9999)]);

        try {
            $pantherClient->request('GET', $url);

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

            $landingUrl = $pantherClient->getCurrentURL();
            $this->urlGuard->assertSchemeIsSupported($landingUrl);
            $this->urlGuard->resolvePublicAddress($landingUrl);
        } finally {
            $pantherClient->quit();
        }

        return $html;
    }

    private function fetchWithHttpClient(ScrapingItem|ScrapingCollection|ScrapingWish $scraping): string
    {
        $headers = ['Accept-Encoding' => 'gzip'];
        foreach ($scraping->getScraper()->getHeaders() ?? [] as $header) {
            $headers[$header['header']] = $header['value'];
        }

        $response = new NoPrivateNetworkHttpClient(new CurlHttpClient())->request('GET', $scraping->getUrl(), [
            'headers' => $headers,
            'extra' => ['curl' => [\CURLOPT_ENCODING => '']],
        ]);

        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException('Scraping error: ' . $response->getStatusCode());
        }

        return $response->getContent();
    }

    private function isPantherAvailable(): bool
    {
        return $this->binaryAvailable(getenv('PANTHER_CHROME_DRIVER_BINARY') ?: null, ['chromedriver'])
            && $this->binaryAvailable(getenv('PANTHER_CHROME_BINARY') ?: null, ['chromium', 'chromium-browser', 'google-chrome', 'chrome']);
    }

    private function binaryAvailable(?string $configuredPath, array $names): bool
    {
        if ($configuredPath !== null) {
            return is_executable($configuredPath);
        }

        $directories = explode(PATH_SEPARATOR, getenv('PATH') ?: '');
        foreach ($names as $name) {
            if (array_any($directories, fn($directory) => $directory !== '' && is_executable($directory . DIRECTORY_SEPARATOR . $name))) {
                return true;
            }
        }

        return false;
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
