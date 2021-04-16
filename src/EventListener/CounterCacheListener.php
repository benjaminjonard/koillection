<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Interfaces\CacheableInterface;
use App\Entity\Item;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Service\CountersCache;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CounterCacheListener
{
    private CountersCache $countersCache;

    public function __construct(CountersCache $countersCache)
    {
        $this->countersCache = $countersCache;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->resetCache($args->getEntity());
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->resetCache($args->getEntity());
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->resetCache($args->getEntity());
    }

    private function resetCache($entity)
    {
        if ($entity instanceof CacheableInterface) {
            $this->countersCache->invalidateCurrentUser();
        }
    }
}
