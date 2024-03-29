<?php

declare(strict_types=1);

namespace App\EventListener\Visibility;

use App\Entity\Album;
use App\Entity\Photo;
use App\Enum\VisibilityEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;

#[AsDoctrineListener(event: Events::prePersist, priority: 1)]
#[AsDoctrineListener(event: Events::onFlush, priority: 1)]
final class AlbumVisibilityListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Album) {
            $entity->setParentVisibility($entity->getParent()?->getFinalVisibility());
            $entity->setFinalVisibility(VisibilityEnum::computeFinalVisibility($entity->getVisibility(), $entity->getParentVisibility()));
        }
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Album) {
                $changeset = $uow->getEntityChangeSet($entity);

                if (\array_key_exists('parent', $changeset)) {
                    $entity->setFinalVisibility(VisibilityEnum::computeFinalVisibility($entity->getVisibility(), $entity->getParent()?->getFinalVisibility()));
                    $this->setVisibilityRecursively($entity, $entity->getFinalVisibility(), $uow, $em);
                }

                if (\array_key_exists('visibility', $changeset)) {
                    $entity->setFinalVisibility(VisibilityEnum::computeFinalVisibility($entity->getVisibility(), $entity->getParentVisibility()));
                    $this->setVisibilityRecursively($entity, $entity->getFinalVisibility(), $uow, $em);
                }
            }
        }
    }

    private function setVisibilityRecursively(Album $entity, string $visibility, UnitOfWork $uow, EntityManagerInterface $em): void
    {
        foreach ($entity->getPhotos() as $photo) {
            $photo->setParentVisibility($visibility);
            $photo->setFinalVisibility(VisibilityEnum::computeFinalVisibility($photo->getVisibility(), $visibility));
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Photo::class), $photo);
        }

        foreach ($entity->getChildren() as $child) {
            $child->setParentVisibility($visibility);
            $child->setFinalVisibility(VisibilityEnum::computeFinalVisibility($child->getVisibility(), $visibility));
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Album::class), $child);

            $this->setVisibilityRecursively($child, $visibility, $uow, $em);
        }
    }


}
