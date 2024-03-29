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
final class ElementVisibilityListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Photo || $entity instanceof Item || $entity instanceof Wish) {
            $parentVisibility = match ($entity::class) {
                Photo::class => $entity->getAlbum()->getFinalVisibility(),
                Item::class => $entity->getCollection()->getFinalVisibility(),
                Wish::class => $entity->getWishlist()->getFinalVisibility()
            };

            $entity->setParentVisibility($parentVisibility);
            $entity->setFinalVisibility($this->computeFinalVisibility($entity->getVisibility(), $parentVisibility));
        }
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Photo || $entity instanceof Item || $entity instanceof Wish) {
                $changeset = $uow->getEntityChangeSet($entity);

                $parentVisibility = match ($entity::class) {
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
