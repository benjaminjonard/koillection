<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

final class OwnershipListener
{
    /**
     * @var Security
     */
    private Security $security;

    /**
     * OwnershipListener constructor.
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (true === property_exists($entity, 'owner') && $entity->getOwner() === null) {
            $entity->setOwner($this->security->getUser());
        }
    }
}
