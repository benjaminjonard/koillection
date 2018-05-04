<?php

namespace App\EventListener\Entity;

use App\Entity\Datum;
use App\Enum\DatumTypeEnum;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class DatumListener
 *
 * @package App\EventListener\Entity
 */
class DatumListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * DatumListener constructor.
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
        if ($entity instanceof Datum) {
            $user = $this->tokenStorage->getToken()->getUser();
            if (DatumTypeEnum::TYPE_SIGN == $entity->getType()) {
                $user->increaseSignsCounter(1);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Datum) {
            $user = $this->tokenStorage->getToken()->getUser();
            if (DatumTypeEnum::TYPE_SIGN == $entity->getType()) {
                $user->increaseSignsCounter(-1);
            }
        }
    }
}
