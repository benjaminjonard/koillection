<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener(event: 'kernel.response')]
final readonly class AuthenticatedResponseCacheListener
{
    public function __construct(
        private Security $security
    ) {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest() || null === $this->security->getUser()) {
            return;
        }

        $event->getResponse()->headers->addCacheControlDirective('no-store');
    }
}
