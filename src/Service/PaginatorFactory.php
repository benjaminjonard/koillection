<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PaginatorFactory
 *
 * @package App\Service
 */
class PaginatorFactory
{
    /**
     * @var RequestStack
     */
    private RequestStack $requestStack;

    /**
     * PaginatorFactory constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function generate(int $totalItems, int $itemsPerPage = 10, string $url = null, array $params = null, $queryParam = 'page') : Paginator
    {
        $url = $url ?? $this->requestStack->getMasterRequest()->getPathInfo();
        $params = $params ?? $this->requestStack->getMasterRequest()->query->all();
        $page = $params[$queryParam] ?? 1;

        unset($params[$queryParam]);

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return new Paginator($totalItems, $itemsPerPage, (int) $page, $url);
    }
}
