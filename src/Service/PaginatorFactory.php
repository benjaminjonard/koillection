<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginatorFactory
{
    public function __construct(
        private RequestStack $requestStack,
        private int $paginationItemsPerPage = 15
    ) {
    }

    public function generate(int $totalItems, string $url = null, array $params = null, $queryParam = 'page'): Paginator
    {
        $url = $url ?? $this->requestStack->getMainRequest()->getPathInfo();
        $params = $params ?? $this->requestStack->getMainRequest()->query->all();
        $page = $params[$queryParam] ?? 1;

        unset($params[$queryParam]);

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return new Paginator($totalItems, $this->paginationItemsPerPage, (int) $page, $url);
    }

    public function getPaginationItemsPerPage(): int
    {
        return $this->paginationItemsPerPage;
    }
}
