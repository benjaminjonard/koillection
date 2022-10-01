<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use App\Service\ContextHandler;
use Twig\Extension\RuntimeExtensionInterface;

class ContextRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ContextHandler $contextHandler
    ) {
    }

    public function getContextUser(): User
    {
        return $this->contextHandler->getContextUser();
    }

    public function applyContext(string $route): string
    {
        return $this->contextHandler->getRouteContext($route);
    }

    public function applyContextTrans(string $trans): string
    {
        $context = $this->contextHandler->getContext();

        if ('shared' === $context) {
            $trans .= '_'.$context;
        }

        return $trans;
    }
}
