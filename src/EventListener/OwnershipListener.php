<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

final class OwnershipListener
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (true === property_exists($entity, 'owner') && null === $entity->getOwner()) {
            $entity->setOwner($this->security->getUser());
        }
    }
}
