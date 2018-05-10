<?php

namespace App\Service;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ContextHandler
 *
 * @package App\Service
 */
class ContextHandler
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var RouterInterface
     */
    private $router;

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
     * @param \Twig_Environment $environment
     * @param RouterInterface $router
     */
    public function __construct(\Twig_Environment $environment, RouterInterface $router)
    {
        $this->environment = $environment;
        $this->router = $router;
    }

    public function init(Request $request)
    {
        preg_match("/^\/(\w+)/", $request->getRequestUri(), $matches);

        if (isset($matches[1]) && in_array($matches[1], ['user', 'preview', 'admin'])) {
            $this->context = $matches[1];
        } else {
            $this->context = 'default';
        }

        $this->environment->addGlobal('context', $this->context);

        if ($this->context == 'user') {
            preg_match("/^\/user\/(\w+)/", $request->getRequestUri(), $matches);
            $this->router->getContext()->setParameter('username', $matches[1]);
        }
    }

    public function getContext() : string
    {
        return $this->context;
    }
}
