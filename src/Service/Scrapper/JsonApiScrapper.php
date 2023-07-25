<?php

declare(strict_types=1);

namespace App\Service\Scrapper;

use App\Entity\Datum;
use App\Entity\Scrapper;
use App\Enum\DatumTypeEnum;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

readonly class JsonApiScrapper
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

        $content = json_decode($response->getContent(), true);

        $data = [];
        foreach ($scrapper->getDataPaths() as $key => $dataPath) {
            $value = $this->extract($dataPath['path'], $content);
            $datum = (new Datum())
                ->setValue($value)
                ->setLabel($dataPath['name'])
                ->setType(DatumTypeEnum::TYPE_TEXT)
                ->setPosition($key)
            ;

            $data[] = [
                DatumTypeEnum::TYPE_TEXT,
                $dataPath['name'],
                $this->twig->render('App/Datum/_datum.html.twig', [
                    'entity' => 'item',
                    'iteration' => '__placeholder__',
                    'type' => DatumTypeEnum::TYPE_TEXT,
                    'datum' => $datum,
                    'label' => $datum->getLabel(),
                    'choiceList' => $datum->getChoiceList(),
                ])
            ];
        }

        return [
            'name' => $this->extract($scrapper->getNamePath(), $content),
            'image' => $this->extract($scrapper->getImagePath(), $content),
            'data' => $data
        ];
    }

    private function extract(string $namePath, $content): string
    {
        dd($content);
        $result = $namePath;

        preg_match_all('/#(.*?)#/', $namePath, $matches);

        foreach ($matches[1] as $path) {
            $currentResult = $content;
            $pathElements = explode('.', $path);

            foreach ($pathElements as $pathElement) {
                if (isset($currentResult[$pathElement])) { // String
                    $currentResult = $currentResult[$pathElement];
                } elseif (isset($currentResult[0][$pathElement])) { // Array
                    $elements = [];
                    foreach ($currentResult as $element) {
                        $elements[] = $element[$pathElement];
                    }
                    $currentResult = implode(', ', $elements);
                } else {
                    throw new \Exception("Scrapping error: <b>$path</b> not found in API response");
                }
            }

            $result = str_replace("#$path#", $currentResult, $result);
        }

        return $result;
    }
}
