<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\ContextHandler;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ContextListener
{
    /**
     * @var ContextHandler
     */
    private ContextHandler $contextHandler;

    /**
     * ContextListener constructor.
     * @param ContextHandler $contextHandler
     */
    public function __construct(ContextHandler $contextHandler)
    {
        $this->contextHandler = $contextHandler;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $this->contextHandler->init($event->getRequest());
    }
}
