<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Scraper;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class ScrapingWish
{
    private ?string $url = null;

    #[Assert\Image(mimeTypes: ['text/html'])]
    private ?File $file = null;

    #[Assert\NotNull]
    private ?Scraper $scraper = null;

    private bool $scrapName = true;

    private bool $scrapImage = true;

    private bool $scrapPrice = true;

    public function __construct(bool $isEdition = false)
    {
        if ($isEdition) {
            $this->scrapName = false;
            $this->scrapImage = false;
        }
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): ScrapingWish
    {
        $this->url = $url;

        return $this;
    }

    public function getScraper(): ?Scraper
    {
        return $this->scraper;
    }

    public function setScraper(?Scraper $scraper): ScrapingWish
    {
        $this->scraper = $scraper;

        return $this;
    }

    public function getScrapName(): bool
    {
        return $this->scrapName;
    }

    public function setScrapName(bool $scrapName): ScrapingWish
    {
        $this->scrapName = $scrapName;

        return $this;
    }

    public function getScrapImage(): bool
    {
        return $this->scrapImage;
    }

    public function setScrapImage(bool $scrapImage): ScrapingWish
    {
        $this->scrapImage = $scrapImage;

        return $this;
    }

    public function getScrapPrice(): bool
    {
        return $this->scrapPrice;
    }

    public function setScrapPrice(bool $scrapPrice): ScrapingWish
    {
        $this->scrapPrice = $scrapPrice;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): ScrapingWish
    {
        $this->file = $file;

        return $this;
    }
}
