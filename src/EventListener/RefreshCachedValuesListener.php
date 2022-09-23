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
use Doctrine\ORM\Event\OnFlushEventArgs;
use function PHPUnit\Framework\matches;

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
            $this->refreshParentEntities($entity);
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $changeset = $uow->getEntityChangeSet($entity);
            if (($entity instanceof Album || $entity instanceof Collection || $entity instanceof Wishlist) && isset($changeset['parent'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['parent'][0]));
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['parent'][1]));
            } elseif ($entity instanceof Item && isset($changeset['collection'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['collection'][0]));
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['collection'][1]));
            } elseif ($entity instanceof Photo && isset($changeset['album'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['album'][0]));
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['album'][1]));
            } elseif ($entity instanceof Wish && isset($changeset['wishlist'])) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['wishlist'][0]));
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($changeset['wishlist'][1]));
            } elseif ($entity instanceof Datum && $entity->getItem() !== null && $entity->getType() === DatumTypeEnum::TYPE_PRICE &&
                      (isset($changeset['value']) || isset($changeset['label']))) {
                $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($entity->getItem()->getCollection()));
            }
        }

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $this->refreshParentEntities($entity);
        }
    }

    private function refreshParentEntities(object $entity): void
    {
        $toRefresh = match(true) {
            $entity instanceof Album, $entity instanceof Collection, $entity instanceof Wishlist => $entity,
            $entity instanceof Item => $entity->getCollection(),
            $entity instanceof Photo => $entity->getAlbum(),
            $entity instanceof Wish => $entity->getWishlist(),
            $entity instanceof Datum && $entity->getType() === DatumTypeEnum::TYPE_PRICE => $entity?->getItem()->getCollection(),
            default => null
        };

        if ($toRefresh) {
            $this->refreshCachedValuesQueue->addEntity($this->getRootEntity($toRefresh));
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
