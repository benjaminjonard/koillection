<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Wishlist;

class RefreshCachedValuesQueue
{
    private array $entities = [];

    public function addEntity(Album|Collection|Wishlist|null $entity): void
    {
        if ($entity !== null && !in_array($entity, $this->entities)) {
            $this->entities[] = $entity;
        }
    }

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function clearEntities()
    {
        $this->entities = [];
    }
}