<?php

namespace App\EventListener;

use App\Service\ContextHandler;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

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
