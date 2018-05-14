<?php

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
     * @var RouterInterface
     */
    private $requestStack;

    /**
     * PaginatorFactory constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function generate($totalItems, $itemsPerPage)
    {
        $url = $this->requestStack->getMasterRequest()->getPathInfo();
        $params = $this->requestStack->getMasterRequest()->query->all();
        $page = $params['page'] ?? 1;
        unset($params['page']);

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return new Paginator($totalItems, $itemsPerPage, $page, $url);
    }
}
