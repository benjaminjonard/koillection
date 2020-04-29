<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\CountersCache;
use Twig\Extension\RuntimeExtensionInterface;

class CountersRuntime implements RuntimeExtensionInterface
{
    private CountersCache $countersCache;

    public function __construct(CountersCache $countersCache)
    {
        $this->countersCache = $countersCache;
    }

    public function getCounters($object)
    {
        return $this->countersCache->getCounters($object);
    }
}