<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\ContextHandler;
use Twig\Extension\RuntimeExtensionInterface;

class ContextRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private ContextHandler $contextHandler
    ) {}

    public function applyContext(string $route) : string
    {
        return $this->contextHandler->getRouteContext($route);
    }

    public function applyContextTrans(string $trans) : string
    {
        $context = $this->contextHandler->getContext();

        if ($context === 'shared') {
            $trans .= '_'.$context;
        }

        return $trans;
    }
}