<?php

namespace App\EventListener\Entity;

use App\Entity\User;
use App\Service\Log\LogQueue;
use App\Service\PasswordUpdater;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class UserListener
 *
 * @package App\EventListener\Entity
 */
class UserListener
{
    /**
     * @var PasswordUpdater
     */
    private $passwordUpdater;

    /**
     * @var LogQueue
     */
    private $logQueue;

    /**
     * UserListener constructor.
     * @param PasswordUpdater $passwordUpdater
     * @param LogQueue $logQueue
     */
    public function __construct(PasswordUpdater $passwordUpdater, LogQueue $logQueue)
    {
        $this->passwordUpdater = $passwordUpdater;
        $this->logQueue = $logQueue;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $this->passwordUpdater->hashPassword($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $this->passwordUpdater->hashPassword($entity);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            //If user is being deleted, we don't want to log anything
            $this->logQueue->disableQueue();
        }
    }
}
