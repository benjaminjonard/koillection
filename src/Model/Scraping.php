<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Scraper;

class Scraping
{
    private string $url;

    private string $entity;

    private ?Scraper $scraper = null;

    public function __construct(string $entity = null)
    {
        $this->setEntity($entity);
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
}
