<?php

namespace App\EventListener;

use App\Entity\User;
use App\Service\ContextHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ActivityListener
 *
 * @package App\EventListener
 */
class ActivityListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ActionListener constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $em
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->tokenStorage= $tokenStorage;
        $this->em= $em;
    }

    /**
     * @param GetResponseEvent $event
     * @return null
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        $now = (new \DateTime())->setTime(0, 0, 0, 0);
        if (!$user instanceof User || $user->getLastDateOfActivity() == $now) {
            return null;
        }

        $user->setLastDateOfActivity($now);
        $this->em->flush();
    }
}
