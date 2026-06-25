<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Album;
use App\Entity\Collection;
use App\Entity\Wishlist;
use App\Service\CachedValuesCalculator;
use App\Service\RefreshCachedValuesQueue;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'kernel.response', priority: 5)]
final readonly class RefreshCachedValuesQueueListener
{
    public function __construct(
        private RefreshCachedValuesQueue $refreshCachedValuesQueue,
        private CachedValuesCalculator $cachedValuesCalculator,
        private ManagerRegistry $managerRegistry
    ) {
    }

    /**
     *   As we are on a kernel response event, this code is triggered at every response.
     *   We have then to check if $em is still opened because if there was a problem
     *   related to Doctrine before this event, the em may have been closed and another error will come up,
     *   hiding the original one making it harder to debug the real error.
     */
    public function onKernelResponse(): void
    {
        $em = $this->managerRegistry->getManager();
        if ($em->isOpen()) {
            $uow = $em->getUnitOfWork();

            // Populate the insert/update schedules. Without this, entities that were
            // dirtied during the request but never flushed by the controller (e.g. a
            // form that failed validation) are not yet "scheduled", so the detach below
            // would miss them and this listener's own flush() would persist them,
            // bypassing validation entirely.
            $uow->computeChangeSets();

            foreach ($uow->getIdentityMap() as $entities) {
                foreach ($entities as $entity) {
                    if ($uow->isScheduledForInsert($entity) || $uow->isScheduledForUpdate($entity)) {
                        $em->detach($entity);
                    }
                }
            }

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
