<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Interfaces\LoggableInterface;
use App\Entity\Log;
use App\Enum\LogTypeEnum;
use Doctrine\ORM\Event\LifecycleEventArgs;

class LoggableListener
{
    public function __construct(
    ) {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof LoggableInterface) {
            $log = (new Log())
                ->setType(LogTypeEnum::TYPE_CREATE)
                ->setObjectId($entity->getId())
                ->setObjectLabel($entity->__toString())
                ->setObjectClass($entity::class)
                ->setOwner($entity->getOwner())
            ;
            $args->getEntityManager()->persist($log);
        }
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof LoggableInterface) {
            $log = (new Log())
                ->setType(LogTypeEnum::TYPE_DELETE)
                ->setObjectId($entity->getId())
                ->setObjectLabel($entity->__toString())
                ->setObjectClass($entity::class)
                ->setOwner($entity->getOwner())
            ;
            $args->getEntityManager()->persist($log);
        }
    }
}
