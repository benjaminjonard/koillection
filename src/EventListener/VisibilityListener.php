<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;

/**
 * Each object has 3 visibility properties :
 * visibility -> the visibility of the object, the only one that can be changed by a user
 * parentVisibility -> the visibility of the object owning the current one
 * finalVisibility -> the visibility used to display or not the object, computed from the 2 previous properties.
 */
class VisibilityListener
{
    private UnitOfWork $uow;
    private EntityManagerInterface $em;

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Album || $entity instanceof Collection || $entity instanceof Wishlist) {
            $entity->setParentVisibility($entity->getParent()?->getFinalVisibility());
            $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $entity->getParentVisibility()));
        }

        if ($entity instanceof Photo || $entity instanceof Item || $entity instanceof Wish) {
            $parentVisibility = match (\get_class($entity)) {
                Photo::class => $entity->getAlbum()->getFinalVisibility(),
                Item::class => $entity->getCollection()->getFinalVisibility(),
                Wish::class => $entity->getWishlist()->getFinalVisibility()
            };

            $entity->setParentVisibility($parentVisibility);
            $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $parentVisibility));
        }
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $this->em = $args->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();

        foreach ($this->uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Album || $entity instanceof Collection || $entity instanceof Wishlist) {
                $this->handleContainer($entity);
            }

            if ($entity instanceof Photo || $entity instanceof Item || $entity instanceof Wish) {
                $this->handleElement($entity);
            }
        }
    }

    private function handleContainer(Album|Collection|Wishlist $entity)
    {
        $changeset = $this->uow->getEntityChangeSet($entity);

        if (\array_key_exists('parent', $changeset)) {
            $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $entity->getParent()?->getFinalVisibility()));
            $this->setVisibilityRecursively($entity, $entity->getFinalVisibility());
        }

        if (\array_key_exists('visibility', $changeset)) {
            $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $entity->getParentVisibility()));
            $this->setVisibilityRecursively($entity, $entity->getFinalVisibility());
        }
    }

    private function handleElement(Photo|Item|Wish $entity)
    {
        $changeset = $this->uow->getEntityChangeSet($entity);
        $parentVisibility = match (\get_class($entity)) {
            Photo::class => $entity->getAlbum()->getFinalVisibility(),
            Item::class => $entity->getCollection()->getFinalVisibility(),
            Wish::class => $entity->getWishlist()->getFinalVisibility()
        };

        if (\array_key_exists('album', $changeset)) {
            $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $parentVisibility));
        }

        if (\array_key_exists('collection', $changeset)) {
            $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $parentVisibility));
        }

        if (\array_key_exists('wishlist', $changeset)) {
            $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $parentVisibility));
        }

        if (\array_key_exists('visibility', $changeset)) {
            $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $parentVisibility));
        }
    }

    private function setVisibilityRecursively(Album|Collection|Wishlist $entity, string $visibility)
    {
        $elements = match (\get_class($entity)) {
            Album::class => $entity->getPhotos(),
            Collection::class => $entity->getItems(),
            Wishlist::class => $entity->getWishes(),
        };

        foreach ($elements as $element) {
            $element->setParentVisibility($visibility);
            $element->setFinalVisibility($this->computeFinalVisibility($element->getVisibility(), $visibility));
            $this->uow->recomputeSingleEntityChangeSet($this->em->getClassMetadata(\get_class($element)), $element);
        }

        foreach ($entity->getChildren() as $child) {
            $child->setParentVisibility($visibility);
            $child->setFinalVisibility($this->computeFinalVisibility($child->getVisibility(), $visibility));
            $this->uow->recomputeSingleEntityChangeSet($this->em->getClassMetadata(\get_class($child)), $child);

            $this->setVisibilityRecursively($child, $visibility);
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
