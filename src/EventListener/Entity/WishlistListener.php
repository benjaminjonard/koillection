<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\Wishlist;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * Class WishlistListener
 *
 * @package App\EventListener\Entity
 */
class WishlistListener
{
    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Wishlist) {
                $changeset = $uow->getEntityChangeSet($entity);

                if (array_key_exists('parent', $changeset)) {
                    if ($entity->getParent()->getVisibility() === VisibilityEnum::VISIBILITY_PRIVATE) {
                        $this->setVisibilityRecursively($entity, $entity->getParent()->getVisibility());
                    }
                }

                if (array_key_exists('visibility', $changeset)) {
                    $this->setVisibilityRecursively($entity, $entity->getVisibility());
                }
            }
        }
    }

    /**
     * @param $wishlist
     * @param $visibility
     */
    public function setVisibilityRecursively($wishlist, $visibility)
    {
        $wishlist->setVisibility($visibility);

        foreach ($wishlist->getWishes() as $item) {
            $item->setVisibility($visibility);
        }

        foreach ($wishlist->getChildren() as $child) {
            $this->setVisibilityRecursively($child, $visibility);
        }
    }
}
