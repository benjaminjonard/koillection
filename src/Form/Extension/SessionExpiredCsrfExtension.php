<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\TokenStorage\SessionTokenStorage;
use Symfony\Contracts\Translation\TranslatorInterface;

class SessionExpiredCsrfExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[\Override]
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $form = $event->getForm();

            if (!$form->isRoot() || !$this->hasCsrfError($form) || $this->sessionHasCsrfToken()) {
                return;
            }

            $errors = iterator_to_array($form->getErrors());
            $form->clearErrors();

            foreach ($errors as $error) {
                if (!$error->getCause() instanceof CsrfToken) {
                    $form->addError($error);
                }
            }

            $form->addError(new FormError($this->translator->trans('error.csrf.session_expired', [], 'validators')));
        }, -10);
    }

    private function hasCsrfError(FormInterface $form): bool
    {
        foreach ($form->getErrors() as $error) {
            if ($error->getCause() instanceof CsrfToken) {
                return true;
            }
        }

        return false;
    }

    private function sessionHasCsrfToken(): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request?->hasSession()) {
            return true;
        }

        return array_any(
            array_keys($request->getSession()->all()),
            static fn (string $key): bool => str_starts_with($key, SessionTokenStorage::SESSION_NAMESPACE . '/')
        );
    }
}
