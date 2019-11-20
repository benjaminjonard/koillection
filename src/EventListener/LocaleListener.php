<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Enum\LocaleEnum;
use Negotiation\LanguageNegotiator;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * Class LocaleListener
 *
 * @package App\EventListener
 */
class LocaleListener
{
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * LocaleListener constructor.
     * @param string $defaultLocale
     */
    public function __construct(string $defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->hasPreviousSession()) {
            $negotiator = new LanguageNegotiator();
            $header = $event->getRequest()->headers->get('Accept-Language');
            
            if (null !== $header) {
                $best = $negotiator->getBest(
                    $event->getRequest()->headers->get('Accept-Language'),
                    LocaleEnum::LOCALES
                );
    
                if (null !== $best) {
                    $request->getSession()->set('_locale', $best->getType());
                    $request->setLocale($request->getSession()->get('_locale', $best->getType()));
                }    
            }

            return;
        }

        if ($request->query->has('_locale')) {
            $locale = $request->query->get('_locale');
            if (\in_array($locale, LocaleEnum::LOCALES, false)) {
                $request->getSession()->set('_locale', $locale);
                $request->setLocale($request->getSession()->get('_locale', $locale));
            }
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
        $session = $event->getRequest()->getSession();

        if (null !== $user->getLocale()) {
            $session->set('_locale', $user->getLocale());
        }
    }
}
