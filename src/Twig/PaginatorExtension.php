<?php

namespace App\Twig;

use App\Model\Paginator;
use App\Service\PaginatorGenerator;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class PaginatorExtension
 *
 * @package App\Twig
 */
class PaginatorExtension extends \Twig_Extension
{
    /**
     * @var PaginatorGenerator
     */
    private $paginatorGenerator;

    /**
     * PaginatorExtension constructor.
     * @param PaginatorGenerator $paginatorGenerator
     */
    public function __construct(PaginatorGenerator $paginatorGenerator)
    {
        $this->paginatorGenerator = $paginatorGenerator;
    }

    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('paginator', [$this, 'buildPaginator']),
        ];
    }

    /**
     * @param $totalItems
     * @param $itemsPerPage
     * @param $currentPage
     * @param $route
     * @param $params
     * @return Paginator
     */
    public function buildPaginator($totalItems, $itemsPerPage, $currentPage, $route, $params)
    {
        return $this->paginatorGenerator->generatePaginator($totalItems, $itemsPerPage, $currentPage, $route, $params);
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'paginator_extension';
    }
}
