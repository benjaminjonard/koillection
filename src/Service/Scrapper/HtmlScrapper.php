<?php

declare(strict_types=1);

namespace App\Service\Scrapper;

use App\Entity\Datum;
use App\Entity\Scrapper;
use App\Enum\DatumTypeEnum;
use App\Service\ArrayTraverser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Intl;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

readonly class HtmlScrapper
{
    public function __construct(
        private HttpClientInterface $client,
        private Environment $twig
    ) {
    }

    public function scrap(Scrapper $scrapper, string $url): array
    {
        $response = $this->client->request(
            'GET',
            $url,
            ['timeout' => 2.5, 'verify_peer' => false, 'verify_host' => false]
        );

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Api error: ' . $response->getStatusCode() . ' - ' . $response->getContent());
        }

        $content = $response->getContent();
        $crawler = new Crawler($content);

        $data = [];
        foreach ($scrapper->getDataPaths() as $key => $dataPath) {
            $value = $this->extract($dataPath['path'], $dataPath['type'], $crawler);

            $datum = (new Datum())
                ->setValue($value)
                ->setLabel($dataPath['name'])
                ->setType($dataPath['type'])
                ->setPosition($key)
            ;

            $data[] = [
                $dataPath['type'],
                $dataPath['name'],
                $this->twig->render('App/Datum/_datum.html.twig', [
                    'entity' => 'item',
                    'iteration' => '__placeholder__',
                    'type' => $dataPath['type'],
                    'datum' => $datum,
                    'label' => $datum->getLabel(),
                    'choiceList' => $datum->getChoiceList(),
                ])
            ];
        }

        return [
            'name' => $this->extract($scrapper->getNamePath(), DatumTypeEnum::TYPE_TEXT, $crawler),
            'image' => $this->extract($scrapper->getImagePath(), DatumTypeEnum::TYPE_TEXT, $crawler),
            'data' => $data
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
            $results =  $crawler->filterXPath($xPath)->each(function (Crawler $node): string {
                return $node->text();
            });

            foreach ($results as $key => $result) {
                if (isset($values[$key])) {
                    $values[$key] = str_replace("#$xPath#", $result, $values[$key]);
                } else {
                    $values[$key] = str_replace("#$xPath#", $result, $template);
                }
            }
        }

        return $this->formatValues($values, $type);
    }

    private function formatValues(?array $values, string $type): ?string
    {
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

            // Else try to match the country name
            return array_flip(Countries::getNames())[$value] ?? null;
        }

        return null;
    }
}