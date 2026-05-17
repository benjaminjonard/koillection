<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Model\ScrapingCollection;
use App\Model\ScrapingItem;
use App\Model\ScrapingWish;
use App\Service\Scraper\ContentScraper;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use JmesPath\Env;
use Twig\Environment;

/**
 * @template ScrapedType
 * @extends ContentScraper<ScrapedType, array>
 */
abstract class JsonScraper extends ContentScraper {
    protected ?CurlHttpClient $client = null;

    public function __construct(Environment $twig) {
        parent::__construct($twig);
        $this->client = new CurlHttpClient();
    }

    protected function getCrawler(ScrapingItem|ScrapingCollection|ScrapingWish $scraping): array
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
        return json_decode($content, true);
    }

    #[\Override]
    protected function extract(?string $template, string $type, $crawler, $scraping): ?string {
        if (!$template) {
            return '';
        }
        $values = [];
        preg_match_all('/#(.*?)#/', $template, $matches);
        foreach($matches[1] as $jmespath) {
            $results = Env::search($jmespath, $crawler);
            if (!is_array($results)) {
                $results = [$results];
            }
            $results = array_map(static function($item): string {
                if (!is_string($item)) {
                    return strval($item);
                }
                return $item;
            }, $results);
            foreach ($results as $key => $result) {
                if (isset($values[$key])) {
                    $values[$key] = str_replace("#{$jmespath}#", $result, $values[$key]);
                } else {
                    $values[$key] = str_replace("#{$jmespath}#", $result, $template);
                }
            }
            foreach ($values as &$value) {
                $value = str_replace("#{$jmespath}#", '', $value);
            }
        }
        return $this->formatValues($values, $type, $scraping);
    }
}