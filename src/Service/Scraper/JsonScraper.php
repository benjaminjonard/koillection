<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Model\ScrapingCollection;
use App\Model\ScrapingItem;
use App\Model\ScrapingWish;
use App\Service\Scraper\ContentScraper;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\JsonPath\JsonCrawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Twig\Environment;

/**
 * @template ScrapedType
 * @extends ContentScraper<ScrapedType, JsonCrawler>
 */
abstract class JsonScraper extends ContentScraper {
    protected ?CurlHttpClient $client = null;

    public function __construct(Environment $twig) {
        parent::__construct($twig);
        $this->client = new CurlHttpClient();
    }

    protected function getCrawler(ScrapingItem|ScrapingCollection|ScrapingWish $scraping): JsonCrawler
    {
        if ($scraping->getFile() instanceof UploadedFile) {
            $content = $scraping->getFile()->getContent();
        } else {
            $headers = [
                'Accept' => 'application/json',
                'Accept-Encoding' => 'gzip, deflate, br, zstd'
            ];
            foreach ($scraping->getScraper()->getHeaders() as $header) {
                $headers[$header['header']] = $header['value'];
            }
            $response = $this->client->request('GET', $scraping->getUrl(), [
                'headers' => $headers
            ]);
            if ($response->getStatusCode() >= 400) {
                throw new \Exception('API error : ' . $response->getStatusCode() . ' - ' . $response->getContent());
            }
            $content = $response->getContent();
        }
        return new JsonCrawler($content);
    }

    #[\Override]
    protected function extract(?string $template, string $type, $crawler, $scraping): ?string {
        if (!$template) {
            return '';
        }
        $values = [];
        preg_match_all('/#(.*?)#/', $template, $matches);
        foreach($matches[1] as $jsonPath) {
            $results = $crawler->find($jsonPath);
            foreach ($results as $key => $result) {
                if (isset($values[$key])) {
                    $values[$key] = str_replace("#{$jsonPath}#", $result, $values[$key]);
                } else {
                    $values[$key] = str_replace("#{$jsonPath}#", $result, $template);
                }
            }
            foreach ($values as &$value) {
                $value = str_replace("#{$jsonPath}#", '', $value);
            }
        }
        return $this->formatValues($values, $type, $scraping);
    }
}