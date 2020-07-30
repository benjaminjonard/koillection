<?php

declare(strict_types=1);

namespace App\Model\Search;

class SearchTag
{
    /**
     * @var string
     */
    private ?string $term = null;

    /**
     * @var int
     */
    private int $page = 1;

    /**
     * @var int
     */
    private int $itemsPerPage;

    public function __construct(int $page, int $itemsPerPage)
    {
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * @return string
     */
    public function getTerm(): ?string
    {
        return $this->term;
    }

    /**
     * @param string $term
     * @return SearchTag
     */
    public function setTerm(string $term): SearchTag
    {
        $this->term = $term;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): ?int
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return SearchTag
     */
    public function setPage(int $page): SearchTag
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return int
     */
    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
    }

    /**
     * @param int $itemsPerPage
     * @return SearchTag
     */
    public function setItemsPerPage(int $itemsPerPage): SearchTag
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }
}
