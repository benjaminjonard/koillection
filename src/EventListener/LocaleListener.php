<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Enum\LocaleEnum;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LocaleListener
{
    /**
     * @var string
     */
    private string $defaultLocale;

    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * LocaleListener constructor.
     * @param SessionInterface $session
     * @param string $defaultLocale
     */
    public function __construct(SessionInterface $session, string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
        $this->session = $session;
    }

    /**
     * @param RequestEvent $event
     */
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

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractivelogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (null !== $user->getLocale()) {
            $this->session->set('_locale', $user->getLocale());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof User) {
            $this->session->set('_locale', $entity->getLocale());
        }
    }
}
