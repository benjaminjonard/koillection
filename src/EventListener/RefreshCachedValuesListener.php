<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Service\RefreshCachedValuesQueue;
use Doctrine\ORM\Event\OnFlushEventArgs;

class RefreshCachedValuesListener
{
    public function __construct(
        private readonly RefreshCachedValuesQueue $refreshCachedValuesQueue
    ) {
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if (in_array($entity::class, [Album::class, Collection::class, Wishlist::class])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity));
            } elseif ($entity instanceof Item) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getCollection()));
            } elseif ($entity instanceof Photo) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getAlbum()));
            } elseif ($entity instanceof Wish) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getWishlist()));
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (in_array($entity::class, [Album::class, Collection::class, Wishlist::class])) {
                $changeset = $uow->getEntityChangeSet($entity);

                if (isset($changeset['parent'])) {
                    $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['parent'][0]));
                    $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['parent'][1]));
                }
            } elseif ($entity instanceof Item) {
                $changeset = $uow->getEntityChangeSet($entity);
                if (isset($changeset['collection'])) {
                    $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['collection'][0]));
                    $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['collection'][1]));
                }
            } elseif ($entity instanceof Photo) {
                $changeset = $uow->getEntityChangeSet($entity);
                if (isset($changeset['album'])) {
                    $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['album'][0]));
                    $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['album'][1]));
                }
            } elseif ($entity instanceof Wish) {
                $changeset = $uow->getEntityChangeSet($entity);
                if (isset($changeset['wishlist'])) {
                    $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['wishlist'][0]));
                    $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['wishlist'][1]));
                }
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if (in_array($entity::class, [Album::class, Collection::class, Wishlist::class])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity));
            } elseif ($entity instanceof Item) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getCollection()));
            } elseif ($entity instanceof Photo) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getAlbum()));
            } elseif ($entity instanceof Wish) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getWishlist()));
            }
        }
    }

    private function getRootEntity(Album|Collection|Wishlist $entity): Album|Collection|Wishlist|null
    {
        while ($entity->getParent() != null) {
            $entity = $entity->getParent();
        }

        return $entity;
    }
}
