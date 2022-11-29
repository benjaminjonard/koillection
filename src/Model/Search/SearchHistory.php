<?php

declare(strict_types=1);

namespace App\Model\Search;

class SearchHistory
{
    private ?string $term = null;

    private array $classes = [];

    private array $types = [];

    public function __construct(
        private int $page = 1,
        private int $itemsPerPage = 10
    ) {
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

    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
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
