<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use App\Service\Log\LoggerChain;
use App\Service\Log\LogQueue;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

class LoggableListener
{
    public function __construct(
        private readonly LoggerChain $loggerChain,
        private readonly LogQueue $logQueue
    ) {
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof LoggableInterface) {
            $this->logQueue->addLog($this->loggerChain->getCreateLog($entity));
        }
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof LoggableInterface) {
                $relations['added'] = [];
                $relations['deleted'] = [];
                $relations['updated'] = [];

                foreach ($uow->getScheduledCollectionUpdates() as $collection) {
                    if ($collection->getOwner()->getId() === $entity->getId()) {
                        foreach ($collection->getInsertDiff() as $relation) {
                            $relations['added'][] = $relation;
                        }
                        foreach ($collection->getDeleteDiff() as $relation) {
                            $relations['deleted'][] = $relation;
                        }
                    }
                }

                $changeset = $uow->getEntityChangeSet($entity);
                $log = $this->loggerChain->getUpdateLog($entity, $changeset, $relations);
                if ($log instanceof Log) {
                    $this->logQueue->addLog($log);
                }
            }
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof LoggableInterface) {
            $this->logQueue->addLog($this->loggerChain->getDeleteLog($entity));
        }
    }
}
