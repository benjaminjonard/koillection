<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Scrapper;

class Scrapping
{
    private string $url;

    private Scrapper $scrapper;

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
}
