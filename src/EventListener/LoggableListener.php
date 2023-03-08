<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use App\Entity\User;
use App\Enum\LogTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;

#[AsDoctrineListener(event: Events::onFlush, priority: -5)]
#[AsDoctrineListener(event: Events::postRemove, priority: -5)]
final class LoggableListener
{
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getObjectManager();
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

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof LoggableInterface) {
            $args->getObjectManager()->createQueryBuilder()
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
}
