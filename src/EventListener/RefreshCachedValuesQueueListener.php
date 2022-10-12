<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Wishlist;
use App\Service\CachedValuesCalculator;
use App\Service\RefreshCachedValuesQueue;
use Doctrine\Persistence\ManagerRegistry;

class RefreshCachedValuesQueueListener
{
    public function __construct(
        private readonly RefreshCachedValuesQueue $refreshCachedValuesQueue,
        private readonly CachedValuesCalculator $cachedValuesCalculator,
        private readonly ManagerRegistry $managerRegistry
    ) {}

    /**
     *   As we are on a kernel response event, this code is triggered at every response.
     *   We have then to check if $em is still opened because if there was a problem
     *   related to Doctrine before this event, the em may have been closed and another error will come up,
     *   hiding the original one making it harder to debug the real error.
     */
    public function onKernelResponse(): void
    {
        if ($this->managerRegistry->getManager()->isOpen()) {
            foreach ($this->refreshCachedValuesQueue->getEntities() as $entity) {
                if ($entity instanceof Album) {
                    $this->cachedValuesCalculator->computeForAlbum($entity);
                } elseif ($entity instanceof Collection) {
                    $this->cachedValuesCalculator->computeForCollection($entity);
                } elseif ($entity instanceof Wishlist) {
                    $this->cachedValuesCalculator->computeForWishlist($entity);
                }
            }

            $this->managerRegistry->getManager()->flush();
        }

        $this->refreshCachedValuesQueue->clearEntities();
    }
}
