<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Enum\LocaleEnum;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LocaleListener
{
    private string $defaultLocale;

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack, string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            return;
        }

        $locale = $request->query->get('_locale');

        if ($locale && \in_array($locale, LocaleEnum::LOCALES, false)) {
            $request->getSession()->set('_locale', $locale);
            $request->setLocale($request->getSession()->get('_locale', $locale));
        } else {
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (null !== $user->getLocale()) {
            $this->requestStack->getSession()->set('_locale', $user->getLocale());
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $this->requestStack->getSession()->set('_locale', $entity->getLocale());
        }
    }
}
