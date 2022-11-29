<?php

declare(strict_types=1);

namespace App\Model\Search;

class SearchTag
{
    private ?string $term = null;

    public function __construct(
        private int $page = 1,
        private int $itemsPerPage = 10
    ) {
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

    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
    }
}
