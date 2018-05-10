<?php

namespace App\Twig;
use App\Service\ContextHandler;

/**
 * Class ContextExtension
 *
 * @package App\Twig
 */
class ContextExtension extends \Twig_Extension
{
    /**
     * @var ContextHandler
     */
    private $contextHandler;

    /**
     * ContextExtension constructor.
     * @param ContextHandler $contextHandler
     */
    public function __construct(ContextHandler $contextHandler)
    {
        $this->contextHandler = $contextHandler;
    }

    /**
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new \Twig_SimpleFilter('applyContext', [$this, 'applyContext']),
            new \Twig_SimpleFilter('applyContextTrans', [$this, 'applyContextTrans'])
        ];
    }

    /**
     * @param string $route
     * @return string
     */
    public function applyContext(string $route) : string
    {
        $context = $this->contextHandler->getContext();

        if (in_array($context, ['user', 'preview'])) {
            $route = str_replace('app_', 'app_'.$context.'_', $route);
        }

        return $route;
    }

    /**
     * @param string $trans
     * @return string
     */
    public function applyContextTrans(string $trans) : string
    {
        $context = $this->contextHandler->getContext();

        if (in_array($context, ['user', 'preview'])) {
            $trans .= '_'.$context;
        }

        return $trans;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'context_extension';
    }
}
