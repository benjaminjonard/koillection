<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Scraper;

class Scraping
{
    private string $url;

    private string $entity;

    private ?Scraper $scraper = null;

    private bool $scrapName = true;

    private bool $scrapImage = true;

    private array $dataToScrap = [];

    public function __construct(string $entity = null, bool $isEdition = false)
    {
        $this->setEntity($entity);

        if ($isEdition) {
            $this->scrapName = false;
            $this->scrapImage = false;
        }
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): Scraping
    {
        $this->url = $url;

        return $this;
    }

    public function getScraper(): ?Scraper
    {
        return $this->scraper;
    }

    public function setScraper(?Scraper $scraper): Scraping
    {
        $this->scraper = $scraper;

        foreach ($scraper->getDataPaths() as $dataPath) {
            $this->dataToScrap[$dataPath['name']] = $dataPath['name'];
        }

        return $this;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): Scraping
    {
        if ($entity !== 'item' && $entity !== 'collection') {
            $entity = 'item';
        }

        $this->entity = $entity;

        return $this;
    }


    public function getScrapName(): bool
    {
        return $this->scrapName;
    }

    public function setScrapName(bool $scrapName): Scraping
    {
        $this->scrapName = $scrapName;

        return $this;
    }

    public function getScrapImage(): bool
    {
        return $this->scrapImage;
    }

    public function setScrapImage(bool $scrapImage): Scraping
    {
        $this->scrapImage = $scrapImage;

        return $this;
    }

    public function getDataToScrap(): array
    {
        return $this->dataToScrap;
    }

    public function setDataToScrap(array $dataToScrap): Scraping
    {
        $this->dataToScrap = $dataToScrap;

        return $this;
    }
}
