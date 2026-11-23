<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Datum;
use App\Entity\Item;
use App\Entity\Photo;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Enum\DatumTypeEnum;
use App\Service\RefreshCachedValuesQueue;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::onFlush)]
final readonly class RefreshCachedValuesListener
{
    public function __construct(
        private RefreshCachedValuesQueue $refreshCachedValuesQueue
    ) {
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getObjectManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->refreshParentEntities($entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $changeset = $uow->getEntityChangeSet($entity);
            if (($entity instanceof Album || $entity instanceof Collection || $entity instanceof Wishlist) && isset($changeset['parent'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['parent'][0]));
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['parent'][1]));
            } elseif (($entity instanceof Album || $entity instanceof Collection || $entity instanceof Wishlist) && isset($changeset['finalVisibility'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity));
            } elseif ($entity instanceof Item && isset($changeset['collection'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['collection'][0]));
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['collection'][1]));
            } elseif ($entity instanceof Item && (isset($changeset['finalVisibility']) || isset($changeset['quantity']))) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getCollection()));
            } elseif ($entity instanceof Photo && isset($changeset['album'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['album'][0]));
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['album'][1]));
            } elseif ($entity instanceof Photo && isset($changeset['finalVisibility'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getAlbum()));
            } elseif ($entity instanceof Wish && isset($changeset['wishlist'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['wishlist'][0]));
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['wishlist'][1]));
            } elseif ($entity instanceof Wish && isset($changeset['finalVisibility'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getWishlist()));
            } elseif ($entity instanceof Datum && isset($changeset['item']) && $this->isPriceUpdate($entity, $changeset)) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['item'][0]?->getCollection()));
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['item'][1]?->getCollection()));
            } elseif ($entity instanceof Datum && $entity->getItem() instanceof Item && $this->isPriceUpdate($entity, $changeset)) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getItem()->getCollection()));
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $this->refreshParentEntities($entity);
        }
    }

    private function refreshParentEntities(object $entity): void
    {
        $toRefresh = match (true) {
            $entity instanceof Album, $entity instanceof Collection, $entity instanceof Wishlist => $entity,
            $entity instanceof Item => $entity->getCollection(),
            $entity instanceof Photo => $entity->getAlbum(),
            $entity instanceof Wish => $entity->getWishlist(),
            $entity instanceof Datum && $entity->getType() === DatumTypeEnum::TYPE_PRICE => $entity?->getItem()->getCollection(),
            default => null
        };

        if ($toRefresh !== null) {
            $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($toRefresh));
        }
    }

    private function isPriceUpdate(Datum $datum, array $changeset): bool
    {
        $types = [$changeset['type'][0] ?? null, $datum->getType()];
        if (!\in_array(DatumTypeEnum::TYPE_PRICE, $types, true)) {
            return false;
        }

        return array_intersect_key($changeset, array_flip(['type', 'label', 'value', 'currency', 'finalVisibility', 'item'])) !== [];
    }

    private function getRootEntity(Album|Collection|Wishlist|null $entity): Album|Collection|Wishlist|null
    {
        if ($entity === null) {
            return null;
        }

        while ($entity->getParent() != null) {
            $entity = $entity->getParent();
        }

        return $entity;
    }
}
