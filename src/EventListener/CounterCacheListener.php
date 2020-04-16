<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Collection;
use App\Entity\Item;
use App\Entity\Wish;
use App\Entity\Wishlist;
use App\Service\CountersCache;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CounterCacheListener
{
    /**
     * @var CountersCache
     */
    private CountersCache $countersCache;

    /**
     * CounterCacheListener constructor.
     * @param CountersCache $countersCache
     */
    public function __construct(CountersCache $countersCache)
    {
        $this->countersCache = $countersCache;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->resetCache($args->getEntity());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->resetCache($args->getEntity());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->resetCache($args->getEntity());
    }

    private function resetCache($entity)
    {
        if ($entity instanceof Collection || $entity instanceof Wishlist || $entity instanceof Item || $entity instanceof Wish) {
            $this->countersCache->reset();
        }
    }
}
