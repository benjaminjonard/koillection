<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\User;
use App\Service\Log\LogQueue;
use App\Service\PasswordUpdater;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserListener
{
    private PasswordUpdater $passwordUpdater;

    private LogQueue $logQueue;

    public function __construct(PasswordUpdater $passwordUpdater, LogQueue $logQueue)
    {
        $this->passwordUpdater = $passwordUpdater;
        $this->logQueue = $logQueue;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $this->passwordUpdater->hashPassword($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $this->passwordUpdater->hashPassword($entity);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            //If user is being deleted, we don't want to log anything
            $this->logQueue->disableQueue();
        }
    }
}
