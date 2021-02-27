<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\ContextHandler;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ContextListener
{
    private ContextHandler $contextHandler;

    public function __construct(ContextHandler $contextHandler)
    {
        $this->contextHandler = $contextHandler;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $this->contextHandler->init($event->getRequest());
    }
}
