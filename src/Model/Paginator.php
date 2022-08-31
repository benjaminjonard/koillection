<?php

declare(strict_types=1);

namespace App\Model;

class Paginator
{
    private int $numPages;

    private int $maxPagesToShow = 5;

    public function __construct(
        private int $totalItems,
        private int $itemsPerPage,
        private int $currentPage,
        private readonly string $url
    ) {
        $this->updateNumPages();
    }

    private function updateNumPages(): void
    {
        $this->numPages = (0 == $this->itemsPerPage ? 0 : (int) ceil($this->totalItems / $this->itemsPerPage));
    }

    public function getMaxPagesToShow(): int
    {
        return $this->maxPagesToShow;
    }

    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setItemsPerPage(int $itemsPerPage): void
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->updateNumPages();
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setTotalItems($totalItems): void
    {
        $this->totalItems = $totalItems;
        $this->updateNumPages();
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getNumPages(): int
    {
        return $this->numPages;
    }

    public function getPageUrl(int|null $pageNum): string
    {
        return $this->url.(parse_url($this->url, PHP_URL_QUERY) ? '&' : '?')."page={$pageNum}";
    }

    public function getNextPage(): ?int
    {
        if ($this->currentPage < $this->numPages) {
            return $this->currentPage + 1;
        }

        return null;
    }

    public function getPrevPage(): ?int
    {
        if ($this->currentPage > 1) {
            return $this->currentPage - 1;
        }

        return null;
    }

    public function getNextUrl(): ?string
    {
        if (null === $this->getNextPage()) {
            return null;
        }

        return $this->getPageUrl($this->getNextPage());
    }

    public function getPrevUrl(): ?string
    {
        if (null === $this->getPrevPage()) {
            return null;
        }

        return $this->getPageUrl($this->getPrevPage());
    }

    /**
     * Get an array of paginated page data.
     *
     * Example:
     * [
     *     ['num' => 1,     'url' => '/example/page/1',  'isCurrent' => false],
     *     ['num' => '...', 'url' => NULL,               'isCurrent' => false],
     *     ['num' => 3,     'url' => '/example/page/3',  'isCurrent' => false],
     *     ['num' => 4,     'url' => '/example/page/4',  'isCurrent' => true],
     *     ['num' => 5,     'url' => '/example/page/5',  'isCurrent' => false],
     *     ['num' => '...', 'url' => NULL,               'isCurrent' => false],
     *     ['num' => 10,    'url' => '/example/page/10', 'isCurrent' => false],
     * ]
     */
    public function getPages(): array
    {
        $pages = [];

        if ($this->numPages <= 1) {
            return [];
        }

        if ($this->numPages <= $this->maxPagesToShow) {
            for ($i = 1; $i <= $this->numPages; ++$i) {
                $pages[] = $this->createPage($i, $i == $this->currentPage);
            }
        } else {
            // Determine the sliding range, centered around the current page.
            $numAdjacents = (int) floor(($this->maxPagesToShow - 3) / 2);
            if ($this->currentPage + $numAdjacents > $this->numPages) {
                $slidingStart = $this->numPages - $this->maxPagesToShow + 2;
            } else {
                $slidingStart = $this->currentPage - $numAdjacents;
            }

            if ($slidingStart < 2) {
                $slidingStart = 2;
            }

            $slidingEnd = $slidingStart + $this->maxPagesToShow - 3;
            if ($slidingEnd >= $this->numPages) {
                $slidingEnd = $this->numPages - 1;
            }

            // Build the list of pages.
            $pages[] = $this->createPage(1, 1 == $this->currentPage);
            if ($slidingStart > 2) {
                $pages[] = $this->createPageEllipsis();
            }

            for ($i = $slidingStart; $i <= $slidingEnd; ++$i) {
                $pages[] = $this->createPage($i, $i == $this->currentPage);
            }

            if ($slidingEnd < $this->numPages - 1) {
                $pages[] = $this->createPageEllipsis();
            }

            $pages[] = $this->createPage($this->numPages, $this->currentPage == $this->numPages);
        }

        return $pages;
    }

    private function createPage(int $pageNum, bool $isCurrent = false): array
    {
        return [
            'num' => $pageNum,
            'url' => $this->getPageUrl($pageNum),
            'isCurrent' => $isCurrent,
        ];
    }

    private function createPageEllipsis(): array
    {
        return [
            'num' => '...',
            'url' => null,
            'isCurrent' => false,
        ];
    }

    public function getCurrentPageFirstItem(): ?int
    {
        $first = ($this->currentPage - 1) * $this->itemsPerPage + 1;
        if ($first > $this->totalItems) {
            return null;
        }

        return $first;
    }

    public function getCurrentPageLastItem(): ?int
    {
        $first = $this->getCurrentPageFirstItem();
        if (null === $first) {
            return null;
        }

        $last = $first + $this->itemsPerPage - 1;
        if ($last > $this->totalItems) {
            return $this->totalItems;
        }

        return $last;
    }
}
