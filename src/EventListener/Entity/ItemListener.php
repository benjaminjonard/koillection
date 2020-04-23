<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\Item;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Event\OnFlushEventArgs;

class ItemListener
{
    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Item) {
                $changeset = $uow->getEntityChangeSet($entity);

                if (\array_key_exists('collection', $changeset)) {
                    if ($entity->getCollection()->getVisibility() === VisibilityEnum::VISIBILITY_PRIVATE) {
                        $entity->setVisibility(VisibilityEnum::VISIBILITY_PRIVATE);
                    }
                }

                if (\array_key_exists('visibility', $changeset)) {
                    foreach ($entity->getData() as $datum) {
                        $datum->setVisibility($entity->getVisibility());
                    }
                }
            }
        }
    }
}
