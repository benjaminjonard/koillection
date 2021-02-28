<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\Wish;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Event\OnFlushEventArgs;

class WishListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Wish) {
                $changeset = $uow->getEntityChangeSet($entity);

                if (\array_key_exists('wishlist', $changeset)) {
                    if ($entity->getWishlist()->getVisibility() === VisibilityEnum::VISIBILITY_PRIVATE) {
                        $entity->setVisibility(VisibilityEnum::VISIBILITY_PRIVATE);
                    }
                }
            }
        }
    }
}
