<?php

declare(strict_types=1);

namespace App\Model\Search;

class SearchHistory
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

    /**
     * @var array
     */
    private array $classes = [];

    /**
     * @var array
     */
    private array $types = [];

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
     * @return SearchHistory
     */
    public function setTerm(string $term): SearchHistory
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
     * @return SearchHistory
     */
    public function setPage(int $page): SearchHistory
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
     * @return SearchHistory
     */
    public function setItemsPerPage(int $itemsPerPage): SearchHistory
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    /**
     * @return array
     */
    public function getClasses(): ?array
    {
        return $this->classes;
    }

    /**
     * @param array $classes
     * @return SearchHistory
     */
    public function setClasses(array $classes): SearchHistory
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * @return array
     */
    public function getTypes(): ?array
    {
        return $this->types;
    }

    /**
     * @param array $types
     * @return SearchHistory
     */
    public function setTypes(array $types): SearchHistory
    {
        $this->types = $types;
        return $this;
    }
}
