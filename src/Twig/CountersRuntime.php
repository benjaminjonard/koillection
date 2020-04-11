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

    public function getTotalItemsCounter($objects)
    {
        $counter = 0;

        foreach ($objects as $object) {
            $counter += $this->countersCache->getCounters($object)['items'];
        }

        return $counter;
    }


    public function getTotalChildrenCounter($objects)
    {
        $counter = 0;

        foreach ($objects as $object) {
            $counter += $this->countersCache->getCounters($object)['children'];
        }

        return $counter;
    }
}