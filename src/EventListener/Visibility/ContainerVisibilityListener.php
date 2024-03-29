<?php

declare(strict_types=1);

namespace App\EventListener\Visibility;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\VisibilityEnum;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;

/**
 * Each object has 3 visibility properties :
 * visibility -> the visibility of the object, the only one that can be changed by a user
 * parentVisibility -> the visibility of the object owning the current one
 * finalVisibility -> the visibility used to display or not the object, computed from the 2 previous properties.
 */
#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::onFlush)]
final class ContainerVisibilityListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Album || $entity instanceof Collection || $entity instanceof Wishlist) {
            $entity->setParentVisibility($entity->getParent()?->getFinalVisibility());
            $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $entity->getParentVisibility()));
        }
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Album || $entity instanceof Collection || $entity instanceof Wishlist) {
                $changeset = $uow->getEntityChangeSet($entity);

                if (\array_key_exists('parent', $changeset)) {
                    $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $entity->getParent()?->getFinalVisibility()));
                    $this->setVisibilityRecursively($entity, $entity->getFinalVisibility(), $uow, $em);
                }

                if (\array_key_exists('visibility', $changeset)) {
                    $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $entity->getParentVisibility()));
                    $this->setVisibilityRecursively($entity, $entity->getFinalVisibility(), $uow, $em);
                }
            }
        }
    }

    private function setVisibilityRecursively(Album|Collection|Wishlist $entity, string $visibility, UnitOfWork $uow, EntityManagerInterface $em): void
    {
        $elements = match ($entity::class) {
            Album::class => $entity->getPhotos(),
            Collection::class => $entity->getItems(),
            Wishlist::class => $entity->getWishes(),
        };

        foreach ($elements as $element) {
            $element->setParentVisibility($visibility);
            $element->setFinalVisibility($this->computeFinalVisibility($element->getVisibility(), $visibility));
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata($element::class), $element);
        }

        foreach ($entity->getChildren() as $child) {
            $child->setParentVisibility($visibility);
            $child->setFinalVisibility($this->computeFinalVisibility($child->getVisibility(), $visibility));
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata($child::class), $child);

            $this->setVisibilityRecursively($child, $visibility, $uow, $em);
        }
    }

    private function computeFinalVisibility(string $visibility, ?string $parentVisibility): string
    {
        if (null === $parentVisibility) {
            return $visibility;
        }

        if (VisibilityEnum::VISIBILITY_PUBLIC === $visibility && VisibilityEnum::VISIBILITY_PUBLIC === $parentVisibility) {
            return VisibilityEnum::VISIBILITY_PUBLIC;
        }

        if (VisibilityEnum::VISIBILITY_PRIVATE === $visibility || VisibilityEnum::VISIBILITY_PRIVATE === $parentVisibility) {
            return VisibilityEnum::VISIBILITY_PRIVATE;
        }

        return VisibilityEnum::VISIBILITY_INTERNAL;
    }
}
