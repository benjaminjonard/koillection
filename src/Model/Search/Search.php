<?php

declare(strict_types=1);

namespace App\Model\Search;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Search
{
    #[Assert\Length(min: 2, minMessage: 'error.search.too_short')]
    private ?string $term = null;

    private ?\DateTime $createdAt = null;

    private bool $searchInItems = true;

    private bool $searchInCollections = true;

    private bool $searchInTags = true;

    private bool $searchInAlbums = true;

    private bool $searchInWishlists = true;

    public function getTerm(): ?string
    {
        return $this->term;
    }

    public function setTerm(string $term): Search
    {
        $this->term = $term;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): Search
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getSearchInItems(): bool
    {
        return $this->searchInItems;
    }

    public function setSearchInItems(bool $searchInItems): Search
    {
        $this->searchInItems = $searchInItems;

        return $this;
    }

    public function getSearchInCollections(): bool
    {
        return $this->searchInCollections;
    }

    public function setSearchInCollections(bool $searchInCollections): Search
    {
        $this->searchInCollections = $searchInCollections;

        return $this;
    }

    public function getSearchInTags(): bool
    {
        return $this->searchInTags;
    }

    public function setSearchInTags(bool $searchInTags): Search
    {
        $this->searchInTags = $searchInTags;

        return $this;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context)
    {
        if (null === $this->getTerm() && null === $this->getCreatedAt()) {
            $context->buildViolation('error.search.empty')
                ->addViolation();
        }
    }

    public function getSearchInAlbums(): bool
    {
        return $this->searchInAlbums;
    }

    public function setSearchInAlbums(bool $searchInAlbums): Search
    {
        $this->searchInAlbums = $searchInAlbums;

        return $this;
    }

    public function getSearchInWishlists(): bool
    {
        return $this->searchInWishlists;
    }

    public function setSearchInWishlists(bool $searchInWishlists): Search
    {
        $this->searchInWishlists = $searchInWishlists;

        return $this;
    }
}
