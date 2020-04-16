<?php

declare(strict_types=1);

namespace App\Model;

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
    private ?string $search = null;

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
     * Get search.
     *
     * @return string
     */
    public function getSearch() : ?string
    {
        return $this->search;
    }

    /**
     * Set search.
     *
     * @param string $search
     *
     * @return Search
     */
    public function setSearch(?string $search) : Search
    {
        $this->search = $search;

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
    public function getSearchInItems() : ?bool
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
    public function setSearchInItems(?bool $searchInItems) : Search
    {
        $this->searchInItems = $searchInItems;

        return $this;
    }

    /**
     * Get searchInCollections.
     *
     * @return bool
     */
    public function getSearchInCollections() : ?bool
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
    public function setSearchInCollections(?bool $searchInCollections) : Search
    {
        $this->searchInCollections = $searchInCollections;

        return $this;
    }

    /**
     * Get searchInTags.
     *
     * @return bool
     */
    public function getSearchInTags() : ?bool
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
    public function setSearchInTags(?bool $searchInTags) : Search
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
        if (null === $this->getSearch() && null === $this->getCreatedAt()) {
            $context->buildViolation('error.search.empty')
                ->addViolation();
        }
    }
}
