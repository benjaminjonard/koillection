<?php

declare(strict_types=1);

namespace App\Model\Search;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Search
{
    /**
     * @var string
     * @Assert\Length(
     *     min=2,
     *     minMessage="error.search.too_short"
     * )
     */
    private ?string $term = null;

    /**
     * @var \DateTime
     */
    private ?\DateTime $createdAt = null;

    /**
     * @var bool
     */
    private bool $searchInItems = true;

    /**
     * @var bool
     */
    private bool $searchInCollections = true;

    /**
     * @var bool
     */
    private bool $searchInTags = true;

    /**
     * @var bool
     */
    private bool $searchInAlbums = true;

    /**
     * @var bool
     */
    private bool $searchInWishlists = true;

    /**
     * @return string
     */
    public function getTerm(): ?string
    {
        return $this->term;
    }

    /**
     * @param string $term
     * @return Search
     */
    public function setTerm(string $term): Search
    {
        $this->term = $term;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt() : ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime
     *
     * @return Search
     */
    public function setCreatedAt(?\DateTime $createdAt) : Search
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get searchInItems.
     *
     * @return bool
     */
    public function getSearchInItems() : bool
    {
        return $this->searchInItems;
    }

    /**
     * Set searchInItems.
     *
     * @param bool
     *
     * @return Search
     */
    public function setSearchInItems(bool $searchInItems) : Search
    {
        $this->searchInItems = $searchInItems;

        return $this;
    }

    /**
     * Get searchInCollections.
     *
     * @return bool
     */
    public function getSearchInCollections() : bool
    {
        return $this->searchInCollections;
    }

    /**
     * Set searchInCollections.
     *
     * @param bool
     *
     * @return Search
     */
    public function setSearchInCollections(bool $searchInCollections) : Search
    {
        $this->searchInCollections = $searchInCollections;

        return $this;
    }

    /**
     * Get searchInTags.
     *
     * @return bool
     */
    public function getSearchInTags() : bool
    {
        return $this->searchInTags;
    }

    /**
     * Set searchInTags.
     *
     * @param bool
     *
     * @return Search
     */
    public function setSearchInTags(bool $searchInTags) : Search
    {
        $this->searchInTags = $searchInTags;

        return $this;
    }

    /**
     * @param ExecutionContextInterface $context
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (null === $this->getTerm() && null === $this->getCreatedAt()) {
            $context->buildViolation('error.search.empty')
                ->addViolation();
        }
    }

    /**
     * @return bool
     */
    public function getSearchInAlbums(): bool
    {
        return $this->searchInAlbums;
    }

    /**
     * @param bool $searchInAlbums
     * @return Search
     */
    public function setSearchInAlbums(bool $searchInAlbums): Search
    {
        $this->searchInAlbums = $searchInAlbums;

        return $this;
    }

    /**
     * @return bool
     */
    public function getSearchInWishlists(): bool
    {
        return $this->searchInWishlists;
    }

    /**
     * @param bool $searchInWishlists
     * @return Search
     */
    public function setSearchInWishlists(bool $searchInWishlists): Search
    {
        $this->searchInWishlists = $searchInWishlists;

        return $this;
    }
}
