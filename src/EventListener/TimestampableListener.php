<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class TimestampableListener
 *
 * @package App\EventListener
 */
final class TimestampableListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (true === property_exists($entity, 'createdAt')) {
            $entity->setCreatedAt(new \DateTime());
        }

        if (true === property_exists($entity, 'loggedAt') && $entity->getLoggedAt() === null) {
            $entity->setLoggedAt(new \DateTime());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (true === property_exists($entity, 'updatedAt')) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }
}
