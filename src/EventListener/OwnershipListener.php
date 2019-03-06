<?php

declare(strict_types=1);

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class OwnershipListener
 *
 * @package App\EventListener
 */
final class OwnershipListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * OwnershipListener constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (true === property_exists($entity, 'owner') && $entity->getOwner() === null) {
            $entity->setOwner($this->tokenStorage->getToken()->getUser());
        }
    }
}
