<?php

declare(strict_types=1);

namespace App\Service;

class FeatureChecker
{
    public function __construct(
        private readonly ContextHandler $contextHandler
    ) {
    }

    public function isFeatureEnabled(string $feature): bool
    {
        $getter = 'is'.ucfirst($feature).'FeatureEnabled';

        return $this->contextHandler->getContextUser()->$getter();
    }
}
