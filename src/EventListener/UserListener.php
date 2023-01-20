<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Service\PasswordUpdater;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class UserListener
{
    public function __construct(
        private readonly PasswordUpdater $passwordUpdater
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof User) {
            $this->passwordUpdater->hashPassword($entity);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof User) {
            $this->passwordUpdater->hashPassword($entity);
        }
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof User) {
            $entity->setWishlistsDisplayConfiguration(null);
            $entity->setCollectionsDisplayConfiguration(null);
            $entity->setAlbumsDisplayConfiguration(null);
            $args->getObjectManager()->flush();
        }
    }
}
