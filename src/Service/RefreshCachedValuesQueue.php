<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Wishlist;
use Doctrine\Persistence\ManagerRegistry;

class RefreshCachedValuesQueue
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly CachedValuesCalculator $cachedValuesCalculator
    ) {
    }

    private array $entities = [];

    public function process(): void
    {
        foreach ($this->getEntities() as $entity) {
            if ($entity instanceof Album) {
                $this->cachedValuesCalculator->computeForAlbum($entity);
            } elseif ($entity instanceof Collection) {
                $this->cachedValuesCalculator->computeForCollection($entity);
            } elseif ($entity instanceof Wishlist) {
                $this->cachedValuesCalculator->computeForWishlist($entity);
            }
        }

        $this->managerRegistry->getManager()->flush();

        $this->clearEntities();
    }

    public function addEntity(Album|Collection|Wishlist|null $entity): void
    {
        if ($entity !== null && !\in_array($entity, $this->entities)) {
            $this->entities[] = $entity;
        }
    }

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function clearEntities(): void
    {
        $this->entities = [];
    }
}
