<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\User;
use App\Entity\Wishlist;
use App\Service\CachedValuesCalculator;
use App\Service\ContextHandler;
use Twig\Extension\RuntimeExtensionInterface;

class ContextRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ContextHandler $contextHandler,
        private readonly CachedValuesCalculator $cachedValuesCalculator
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
            $trans .= '_' . $context;
        }

        return $trans;
    }

    public function getCachedValues(Collection|Album|Wishlist $entity)
    {
        return $this->cachedValuesCalculator->getCachedValues($entity);
    }
}
