<?php

declare(strict_types=1);

namespace App\Model\Search;

class SearchHistory
{
    private ?string $term = null;

    private int $page = 1;

    private int $itemsPerPage;

    private array $classes = [];

    private array $types = [];

    public function __construct(int $page, int $itemsPerPage)
    {
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getTerm(): ?string
    {
        return $this->term;
    }

    public function setTerm(string $term): SearchHistory
    {
        $this->term = $term;

        return $this;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(int $page): SearchHistory
    {
        $this->page = $page;

        return $this;
    }

    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage): SearchHistory
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    public function getClasses(): ?array
    {
        return $this->classes;
    }

    public function setClasses(array $classes): SearchHistory
    {
        $this->classes = $classes;
        return $this;
    }

    public function getTypes(): ?array
    {
        return $this->types;
    }

    public function setTypes(array $types): SearchHistory
    {
        $this->types = $types;
        return $this;
    }
}
