<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\ConfigurationEnum;

class FeatureChecker
{
    public function __construct(
        private readonly ContextHandler $contextHandler,
        private readonly ConfigurationHelper $configurationHelper
    ) {
    }

    public function isFeatureEnabled(string $feature): bool
    {
        if (!$this->isFeatureEnabledOnInstance($feature)) {
            return false;
        }

        $getter = 'is' . ucfirst($feature) . 'FeatureEnabled';

        return $this->contextHandler->getContextUser()->$getter();
    }

    public function isFeatureEnabledOnInstance(string $feature): bool
    {
        $label = ConfigurationEnum::INSTANCE_FEATURES[$feature] ?? null;

        return $label === null || $this->configurationHelper->getValue($label) === '1';
    }
}
