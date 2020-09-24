<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class FeatureChecker
{
    /**
     * @var ContextHandler
     */
    private ContextHandler $contextHandler;

    public function __construct(ContextHandler $contextHandler)
    {
        $this->contextHandler = $contextHandler;
    }

    public function isFeatureEnabled($feature)
    {
        $getter = 'is' . ucfirst($feature) . 'FeatureActive';

        return $this->contextHandler->getContextUser()->$getter();
    }
}
