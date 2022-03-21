<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Interfaces\CacheableInterface;
use App\Service\CountersCache;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CounterCacheListener
{
    public function __construct(
        private CountersCache $countersCache
    ) {
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $this->resetCache($args->getEntity());
    }

    public function postUpdate(LifecycleEventArgs $args): void
    {
        $this->resetCache($args->getEntity());
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->resetCache($args->getEntity());
    }

    private function resetCache(object $entity): void
    {
        if ($entity instanceof CacheableInterface) {
            $this->countersCache->invalidateCurrentUser();
        }
    }
}
