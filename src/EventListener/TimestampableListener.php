<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
final class TimestampableListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if (property_exists($entity, 'createdAt')) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if (property_exists($entity, 'loggedAt') && null === $entity->getLoggedAt()) {
            $entity->setLoggedAt(new \DateTimeImmutable());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if (property_exists($entity, 'updatedAt')) {
            $entity->setUpdatedAt(new \DateTimeImmutable());
        }
    }
}
