<?php

namespace App\EventListener;

use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use App\Service\Log\LoggerChain;
use App\Service\Log\LogQueue;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * Class LoggableListener
 *
 * @package App\EventListener
 */
class LoggableListener
{
    /**
     * @var LoggerChain
     */
    private $loggerChain;

    /**
     * @var LogQueue
     */
    private $logQueue;

    /**
     * LoggableListener constructor.
     * @param LoggerChain $loggerChain
     * @param LogQueue $logQueue
     */
    public function __construct(LoggerChain $loggerChain, LogQueue $logQueue)
    {
        $this->loggerChain = $loggerChain;
        $this->logQueue = $logQueue;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof LoggableInterface) {
            $this->logQueue->addLog($this->loggerChain->getCreateLog($entity));
        }
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
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

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof LoggableInterface) {
            $this->logQueue->addLog($this->loggerChain->getDeleteLog($entity));
        }
    }
}
