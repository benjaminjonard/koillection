<?php

declare(strict_types=1);

namespace App\Model\Search;

class SearchTag
{
    private ?string $term = null;

    private int $page = 1;

    private int $itemsPerPage;

    public function __construct(int $page, int $itemsPerPage)
    {
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getTerm(): ?string
    {
        return $this->term;
    }

    public function setTerm(string $term): SearchTag
    {
        $this->term = $term;

        return $this;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(int $page): SearchTag
    {
        $this->page = $page;

        return $this;
    }

    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage): SearchTag
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }
}
