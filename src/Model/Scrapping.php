<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Scrapper;

class Scrapping
{
    private string $url;

    private string $entity;

    private Scrapper $scrapper;

    public function __construct(string $entity = null)
    {
        $this->setEntity($entity);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): Scrapping
    {
        $this->url = $url;

        return $this;
    }

    public function getScrapper(): Scrapper
    {
        return $this->scrapper;
    }

    public function setScrapper(Scrapper $scrapper): Scrapping
    {
        $this->scrapper = $scrapper;

        return $this;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): Scrapping
    {
        if ($entity !== 'item' && $entity !== 'collection') {
            $entity = 'item';
        }

        $this->entity = $entity;

        return $this;
    }
}
