<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\ContextHandler;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: 'kernel.request')]
final readonly class ContextListener
{
    public function __construct(
        private ContextHandler $contextHandler
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->contextHandler->init($event->getRequest());
    }
}
