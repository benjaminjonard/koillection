<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AccessDeniedListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        if ($event->getThrowable() instanceof AccessDeniedHttpException) {
            $event->setThrowable(new NotFoundHttpException());
        }
    }
}
