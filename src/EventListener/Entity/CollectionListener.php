<?php

declare(strict_types=1);

namespace App\EventListener\Entity;

use App\Entity\Collection;
use App\Enum\VisibilityEnum;
use Doctrine\ORM\Event\OnFlushEventArgs;

class CollectionListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
            if ($entity instanceof Collection) {
                $changeset = $uow->getEntityChangeSet($entity);
                if (\array_key_exists('parent', $changeset)) {
                    if ($entity->getParent()->getVisibility() === VisibilityEnum::VISIBILITY_PRIVATE) {
                        $this->setVisibilityRecursively($entity, $entity->getParent()->getVisibility());
                    }
                }

                if (\array_key_exists('visibility', $changeset)) {
                    $this->setVisibilityRecursively($entity, $entity->getVisibility());
                    foreach ($entity->getData() as $datum) {
                        $datum->setVisibility($entity->getVisibility());
                    }
                }
            }
        }
    }

    public function setVisibilityRecursively(Collection $collection, $visibility)
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
