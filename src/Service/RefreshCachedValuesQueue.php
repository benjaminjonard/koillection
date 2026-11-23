<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Wishlist;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Service\ResetInterface;

class RefreshCachedValuesQueue implements ResetInterface
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly CachedValuesCalculator $cachedValuesCalculator
    ) {
    }

    private array $entities = [];

    public function process(): void
    {
        $this->cachedValuesCalculator->compute($this->getEntities());

        $this->managerRegistry->getManager()->flush();

        $this->clearEntities();
    }

    public function addEntity(Album|Collection|Wishlist|null $entity): void
    {
        if ($entity !== null && !\in_array($entity, $this->entities, true)) {
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

    public function reset(): void
    {
        $this->entities = [];
    }
}
