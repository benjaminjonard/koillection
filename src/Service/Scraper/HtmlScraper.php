<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Entity\Datum;
use App\Enum\DatumTypeEnum;
use App\Model\Scraping;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Intl\Countries;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

readonly class HtmlScraper
{
    public function __construct(
        private HttpClientInterface $client,
        private Environment $twig
    ) {
    }

    public function scrap(Scraping $scraping): array
    {
        $scraper = $scraping->getScraper();

        if ($scraping->getFile() instanceof UploadedFile) {
            $content = $scraping->getFile()->getContent();
        } else {
            $response = $this->client->request(
                'GET',
                $scraping->getUrl(),
                ['timeout' => 2.5, 'verify_peer' => false, 'verify_host' => false]
            );

            if (200 !== $response->getStatusCode()) {
                throw new \Exception('Api error: ' . $response->getStatusCode() . ' - ' . $response->getContent());
            }

            $content = $response->getContent();
        }

        $crawler = new Crawler($content);

        return [
            'name' => $scraping->getScrapName() ? $this->extract($scraper->getNamePath(), DatumTypeEnum::TYPE_TEXT, $crawler) : null,
            'image' => $scraping->getScrapImage() ? $this->extract($scraper->getImagePath(), DatumTypeEnum::TYPE_TEXT, $crawler) : null,
            'data' => $this->scrapData($scraping, $crawler),
            'scrapedUrl' => $scraping->getUrl()
        ];
    }

    private function extract(?string $template, string $type, Crawler $crawler): ?string
    {
        if (!$template) {
            return '';
        }

        $values = [];
        preg_match_all('/#(.*?)#/', $template, $matches);

        foreach ($matches[1] as $xPath) {
            $results =  $crawler->evaluate($xPath);

            if ($results instanceof Crawler) {
                $results =  $results->each(function (Crawler $node): string {
                    return $node->text();
                });
            }

            foreach ($results as $key => $result) {
                if (isset($values[$key])) {
                    $values[$key] = str_replace("#$xPath#", $result, $values[$key]);
                } else {
                    $values[$key] = str_replace("#$xPath#", $result, $template);
                }
            }

            // Remove xPath from result in case nothing was found
            foreach ($values as &$value) {
                $value = str_replace("#$xPath#", '', $value);
            }
        }

        return $this->formatValues($values, $type);
    }

    private function scrapData(Scraping $scraping, Crawler $crawler) : array
    {
        $data = [];

        foreach ($scraping->getDataToScrap() as $key => $dataToScrap) {
            $value = $this->extract($dataToScrap->getPath(), $dataToScrap->getType(), $crawler);

            $datum = (new Datum())
                ->setValue($value)
                ->setLabel($dataToScrap->getName())
                ->setType($dataToScrap->getType())
                ->setPosition((int) $key)
            ;

            $data[] = [
                $dataToScrap->getType(),
                $dataToScrap->getName(),
                $this->twig->render('App/Datum/_datum.html.twig', [
                    'entity' => $scraping->getEntity(),
                    'iteration' => '__placeholder__',
                    'type' => $dataToScrap->getType(),
                    'datum' => $datum,
                    'label' => $datum->getLabel(),
                    'choiceList' => $datum->getChoiceList(),
                ])
            ];
        }

        return $data;
    }

    private function formatValues(?array $values, string $type): ?string
    {
        if ($values === null || $values === []) {
            return null;
        }

        if ($type === DatumTypeEnum::TYPE_TEXT) {
            return implode(', ', $values);
        }

        if ($type === DatumTypeEnum::TYPE_LIST) {
            return json_encode($values);
        }

        if ($type === DatumTypeEnum::TYPE_COUNTRY) {
            $value = array_shift($values);

            // Try to match alpha2 code
            if (strlen($value) === 2 && Countries::exists($value)) {
                return $value;
            }

            // Try to match alpha3 code
            if (strlen($value) === 3 && Countries::alpha3CodeExists($value)) {
                return $value;
            }

            // Else try to match the country name
            return array_flip(Countries::getNames())[$value] ?? null;
        }

        return null;
    }
}