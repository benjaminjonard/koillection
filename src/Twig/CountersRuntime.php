<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\CountersCache;
use Twig\Extension\RuntimeExtensionInterface;

class CountersRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private CountersCache $countersCache
    ) {
    }

    public function getCounters($object): array
    {
        return $this->countersCache->getCounters($object);
    }
}
