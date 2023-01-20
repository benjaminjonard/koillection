<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class OwnershipListener
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (property_exists($entity, 'owner') && null === $entity->getOwner()) {
            $entity->setOwner($this->security->getUser());
        }
    }
}
