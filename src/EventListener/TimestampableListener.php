<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

final class TimestampableListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (property_exists($entity, 'createdAt')) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if (property_exists($entity, 'loggedAt') && null === $entity->getLoggedAt()) {
            $entity->setLoggedAt(new \DateTimeImmutable());
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (property_exists($entity, 'updatedAt')) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
        }
    }
}
