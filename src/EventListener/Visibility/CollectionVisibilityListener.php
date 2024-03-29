<?php

declare(strict_types=1);

namespace App\EventListener\Visibility;

use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Enum\VisibilityEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;

#[AsDoctrineListener(event: Events::prePersist, priority: -1)]
#[AsDoctrineListener(event: Events::onFlush, priority: -1)]
final class CollectionVisibilityListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Collection) {
            $entity->setParentVisibility($entity->getParent()?->getFinalVisibility());
            $entity->updateFinalVisibility();
        }
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Collection) {
                $changeset = $uow->getEntityChangeSet($entity);

                if (\array_key_exists('parent', $changeset)) {
                    $entity->updateFinalVisibility();
                    $this->setVisibilityRecursively($entity, $entity->getFinalVisibility(), $uow, $em);
                }

                if (\array_key_exists('visibility', $changeset)) {
                    $entity->updateFinalVisibility();
                    $this->setVisibilityRecursively($entity, $entity->getFinalVisibility(), $uow, $em);
                }
            }
        }
    }

    private function setVisibilityRecursively(Collection $entity, string $visibility, UnitOfWork $uow, EntityManagerInterface $em): void
    {
        // Collection's data
        foreach ($entity->getData() as $datum) {
            $datum->setParentVisibility($visibility);
            $entity->updateFinalVisibility();
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Datum::class), $datum);
        }

        // Collection's items
        foreach ($entity->getItems() as $item) {
            $item->setParentVisibility($visibility);
            $entity->updateFinalVisibility();
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Item::class), $item);

            // Each item's data
            foreach ($item->getData() as $datum) {
                $datum->setParentVisibility($item->getFinalVisibility());
                $entity->updateFinalVisibility();
                $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Datum::class), $datum);
            }
        }

        // Collection's children
        foreach ($entity->getChildren() as $child) {
            $child->setParentVisibility($visibility);
            $entity->updateFinalVisibility();
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Collection::class), $child);

            $this->setVisibilityRecursively($child, $visibility, $uow, $em);
        }
    }


}
