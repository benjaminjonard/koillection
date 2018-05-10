<?php

namespace App\Service;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ContextHandler
 *
 * @package App\Service
 */
class ContextHandler
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var string
     * Possible values are :
     * user: Public pages
     * preview: Preview pages
     * admin: Admin pages
     * default: everything else
     */
    private $context;

    /**
     * ContextHandler constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getCurrentRequestContext() : string
    {
        if ($this->context) {
            return $this->context;
        }

        preg_match("/^\/(\w+)/", $this->requestStack->getMasterRequest()->getRequestUri(), $matches);
        switch ($matches[1]) {
            case 'user':
            case 'preview':
            case 'admin':
                $this->context = $matches[1];
                break;
            default:
                $this->context = 'default';
                break;
        }

        return $this->context;
    }
}
