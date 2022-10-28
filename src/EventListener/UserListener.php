<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Service\PasswordUpdater;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserListener
{
    public function __construct(
        private readonly PasswordUpdater $passwordUpdater
    ) {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof User) {
            $this->passwordUpdater->hashPassword($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof User) {
            $this->passwordUpdater->hashPassword($entity);
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
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
