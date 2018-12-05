<?php

namespace App\EventListener\Entity;

use App\Entity\Collection;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * Class CollectionListener
 *
 * @package App\EventListener\Entity
 */
class CollectionListener
{
    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Collection) {
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
     * @param $collection
     * @param $visibility
     */
    public function setVisibilityRecursively($collection, $visibility)
    {
        $collection->setVisibility($visibility);

        foreach ($collection->getItems() as $item) {
            $item->setVisibility($visibility);
            foreach ($item->getData() as $datum) {
                $datum->setVisibility($visibility);
            }
        }

        foreach ($collection->getChildren() as $child) {
            $this->setVisibilityRecursively($child, $visibility);
        }
    }
}
