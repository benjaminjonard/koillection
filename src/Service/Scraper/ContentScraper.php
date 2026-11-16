<?php

declare(strict_types=1);

namespace App\Service\Scraper;

use App\Entity\Datum;
use App\Enum\DatumTypeEnum;
use App\Model\ScrapingCollection;
use App\Model\ScrapingItem;
use App\Model\ScrapingWish;
use Symfony\Component\Intl\Countries;
use Twig\Environment;

/**
 * @template ScrapedType
 * @template CrawlerType
 *
 * @implements Scraper<ScrapedType>
 */
abstract class ContentScraper implements Scraper
{
    public function __construct(
        protected Environment $twig
    ) {
    }

    protected function guessHost(?string $url, ScrapingItem|ScrapingCollection|ScrapingWish $scraping): ?string
    {
        if ($url === null || $scraping->getUrl() === null) {
            return null;
        }

        $urlElements = parse_url($url);
        if (!isset($urlElements['host'])) {
            $scrapingUrlElements = parse_url($scraping->getUrl());
            $url = $scrapingUrlElements['scheme'] . '://' . $scrapingUrlElements['host'] . ($urlElements['path'] ?? '');
            if (isset($urlElements['query'])) {
                $url .= '?' . $urlElements['query'];
            }
        }

        return $url;
    }

    protected function formatValues(?array $values, string $type, $scraping): ?string
    {
        if ($values === null || $values === []) {
            return null;
        }

        if ($type === DatumTypeEnum::TYPE_TEXT) {
            return implode(', ', array_unique($values));
        }

        if ($type === DatumTypeEnum::TYPE_LIST) {
            return json_encode(array_values(array_unique($values)));
        }

        if ($type === DatumTypeEnum::TYPE_TEXTAREA) {
            return $values[0];
        }

        if ($type === DatumTypeEnum::TYPE_COUNTRY) {
            $value = trim((string) array_shift($values));

            // Try to match alpha2 code
            if (\strlen($value) === 2 && Countries::exists(strtoupper($value))) {
                return strtoupper($value);
            }

            // Try to match alpha3 code
            if (\strlen($value) === 3 && Countries::alpha3CodeExists(strtoupper($value))) {
                return Countries::getAlpha2Code(strtoupper($value));
            }

            // Else try to match the country name
            foreach ([null, 'en'] as $locale) {
                foreach (Countries::getNames($locale) as $code => $name) {
                    if (mb_strtolower($name) === mb_strtolower($value)) {
                        return $code;
                    }
                }
            }

            return null;
        }

        if ($type === DatumTypeEnum::TYPE_PRICE) {
            $value = preg_replace('/[^0-9.,]/', '', (string) array_shift($values));
            $value = preg_match('/^(.*)[.,](\d{1,2})$/', $value, $matches) === 1
                ? preg_replace('/[.,]/', '', $matches[1]) . '.' . $matches[2]
                : preg_replace('/[.,]/', '', $value);

            return is_numeric($value) ? $value : null;
        }

        if ($type === DatumTypeEnum::TYPE_IMAGE) {
            return $this->guessHost($values[0], $scraping);
        }

        if ($type === DatumTypeEnum::TYPE_LINK) {
            return $this->guessHost($values[0], $scraping);
        }

        return null;
    }

    /**
     * @param CrawlerType $crawler
     */
    abstract protected function extract(?string $template, string $type, $crawler, $scraping): ?string;

    /**
     * @param CrawlerType $crawler
     */
    protected function scrapData(ScrapingItem|ScrapingCollection|ScrapingWish $scraping, $crawler, string $entityType): array
    {
        $data = [];

        foreach ($scraping->getDataToScrap() as $key => $dataToScrap) {
            $value = $this->extract($dataToScrap->getPath(), $dataToScrap->getType(), $crawler, $scraping);

            $datum = (new Datum())
                ->setValue($value)
                ->setLabel($dataToScrap->getName())
                ->setType($dataToScrap->getType())
                ->setPosition((int) $key)
            ;

            $data[] = [
                \in_array($dataToScrap->getType(), [DatumTypeEnum::TYPE_IMAGE, DatumTypeEnum::TYPE_SIGN]) ? 'image' : 'text',
                $dataToScrap->getName(),
                $this->twig->render('App/Datum/_datum.html.twig', [
                    'entity' => $entityType,
                    'iteration' => '__placeholder__',
                    'type' => $dataToScrap->getType(),
                    'datum' => $datum,
                    'label' => $datum->getLabel(),
                    'choiceList' => $datum->getChoiceList(),
                    'visibility' => $datum->getVisibility()
                ])
            ];
        }

        return $data;
    }
}
