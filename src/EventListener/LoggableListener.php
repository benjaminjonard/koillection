<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use App\Enum\LogTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;

class LoggableListener
{
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof LoggableInterface) {
                $this->persistLog($em, $uow, $entity, LogTypeEnum::TYPE_CREATE);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof LoggableInterface) {
                $this->persistLog($em, $uow, $entity, LogTypeEnum::TYPE_DELETE);
            }
        }
    }

    private function persistLog(EntityManagerInterface $em, UnitOfWork $uow, LoggableInterface $entity, string $type): void
    {
        $log = (new Log())
            ->setType($type)
            ->setObjectId($entity->getId())
            ->setObjectLabel($entity->__toString())
            ->setObjectClass($entity::class)
            ->setOwner($entity->getOwner())
        ;

        $em->persist($log);
        $classMetadata = $em->getClassMetadata(Log::class);
        $uow->computeChangeSet($classMetadata, $log);
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof LoggableInterface) {
            $args->getEntityManager()->createQueryBuilder()
                ->update(Log::class, 'l')
                ->set('l.objectDeleted', '?1')
                ->where('l.objectId = ?2')
                ->setParameter(1, true)
                ->setParameter(2, $entity->getId())
                ->getQuery()
                ->execute()
            ;
        }
    }
}
