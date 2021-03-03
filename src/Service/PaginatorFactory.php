<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginatorFactory
{
    private RequestStack $requestStack;

    private int $paginationItemsPerPage;

    public function __construct(RequestStack $requestStack, int $paginationItemsPerPage)
    {
        $this->requestStack = $requestStack;
        $this->paginationItemsPerPage = $paginationItemsPerPage;
    }

    public function generate(int $totalItems, string $url = null, array $params = null, $queryParam = 'page') : Paginator
    {
        $url = $url ?? $this->requestStack->getMasterRequest()->getPathInfo();
        $params = $params ?? $this->requestStack->getMasterRequest()->query->all();
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
