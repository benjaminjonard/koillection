<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\ContextHandler;
use Twig\Extension\RuntimeExtensionInterface;

class ContextRuntime implements RuntimeExtensionInterface
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
     * @param string $route
     * @return string
     */
    public function applyContext(string $route) : string
    {
        return $this->contextHandler->getRouteContext($route);
    }

    /**
     * @param string $trans
     * @return string
     */
    public function applyContextTrans(string $trans) : string
    {
        $context = $this->contextHandler->getContext();

        if (\in_array($context, ['user', 'preview'])) {
            $trans .= '_'.$context;
        }

        return $trans;
    }
}