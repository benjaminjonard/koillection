<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\ContextHandler;
use Twig\Extension\RuntimeExtensionInterface;

class ContextRuntime implements RuntimeExtensionInterface
{
    private ContextHandler $contextHandler;

    public function __construct(ContextHandler $contextHandler)
    {
        $this->contextHandler = $contextHandler;
    }

    public function applyContext(string $route) : string
    {
        return $this->contextHandler->getRouteContext($route);
    }

    public function applyContextTrans(string $trans) : string
    {
        $context = $this->contextHandler->getContext();

        if ($context === 'user') {
            $trans .= '_'.$context;
        }

        return $trans;
    }
}