<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\ColorPicker;
use Doctrine\ORM\Event\PrePersistEventArgs;

class ColorListener
{
    public function __construct(
        private readonly ColorPicker $colorPicker
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if (property_exists($entity, 'color') && null === $entity->getColor()) {
            $entity->setColor($this->colorPicker->pickRandomColor());
        }
    }
}
