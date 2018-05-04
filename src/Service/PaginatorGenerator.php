<?php

namespace App\Service;

use App\Model\Paginator;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class PaginatorGenerator
 *
 * @package App\Service
 */
class PaginatorGenerator
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * PaginatorGenerator constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function generatePaginator($totalItems, $itemsPerPage, $currentPage, $route, $params)
    {
        return new Paginator($this->router, $totalItems, $itemsPerPage, $currentPage, $route, $params);
    }
}
