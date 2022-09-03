<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\ColorPicker;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ColorListener
{
    public function __construct(
        private readonly ColorPicker $colorPicker
    ) {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (property_exists($entity, 'color') && null === $entity->getColor()) {
            $entity->setColor($this->colorPicker->pickRandomColor());
        }
    }
}
