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
 * Class ContextListener
 *
 * @package App\EventListener
 */
class ContextListener
{
    /**
     * @var ContextHandler
     */
    private $contextHandler;

    /**
     * ContextListener constructor.
     * @param ContextHandler $contextHandler
     */
    public function __construct(ContextHandler $contextHandler)
    {
        $this->contextHandler = $contextHandler;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->contextHandler->init($event->getRequest());
    }
}
