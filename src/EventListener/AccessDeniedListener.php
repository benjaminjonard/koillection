<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class AccessDeniedListener
 *
 * @package App\EventListener
 */
class AccessDeniedListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->getException() instanceof AccessDeniedHttpException) {
            $event->setException(new NotFoundHttpException());
        }
    }
}
