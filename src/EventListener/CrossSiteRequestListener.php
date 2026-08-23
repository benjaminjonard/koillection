<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[AsEventListener(event: 'kernel.request', priority: 512)]
final readonly class CrossSiteRequestListener
{
    private const SAME_ORIGIN = ['same-origin', 'none'];

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($this->isCrossSite($event->getRequest())) {
            throw new AccessDeniedHttpException('Cross-origin request rejected.');
        }
    }

    private function isCrossSite(Request $request): bool
    {
        if ($request->isMethodSafe()) {
            return false;
        }

        if (str_starts_with($request->getPathInfo(), '/api')) {
            return false;
        }

        $site = $request->headers->get('Sec-Fetch-Site');

        return null !== $site && !\in_array($site, self::SAME_ORIGIN, true);
    }
}
