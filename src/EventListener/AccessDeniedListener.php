<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsEventListener(event: 'kernel.exception', priority: 255)]
final class AccessDeniedListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        if ($event->getThrowable() instanceof AccessDeniedHttpException) {
            $event->setThrowable(new NotFoundHttpException());
        }
    }
}
