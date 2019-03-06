<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\Wish;
use App\Enum\VisibilityEnum;
use App\Service\ColorPicker;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * Class WishListener
 *
 * @package App\EventListener\Entity
 */
class WishListener
{
    /**
     * @var ColorPicker
     */
    private $colorPicker;

    /**
     * WishListener constructor.
     * @param ColorPicker $colorPicker
     */
    public function __construct(ColorPicker $colorPicker)
    {
        $this->colorPicker = $colorPicker;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Wish) {
            $entity->setColor($this->colorPicker->pickRandomColor());
        }
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Wish) {
                $changeset = $uow->getEntityChangeSet($entity);

                if (array_key_exists('wishlist', $changeset)) {
                    if ($entity->getWishlist()->getVisibility() === VisibilityEnum::VISIBILITY_PRIVATE) {
                        $entity->setVisibility(VisibilityEnum::VISIBILITY_PRIVATE);
                    }
                }
            }
        }
    }
}
