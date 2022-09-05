<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class UsernameOrEmailPasswordAuthenticator extends AbstractLoginFormAuthenticator
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RouterInterface $router
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $login = $request->request->get('_login');
        $password = $request->request->get('_password');
        $csrfToken = $request->request->get('_csrf_token');

        $request->getSession()->set(Security::LAST_USERNAME, $login);

        return new Passport(
            new UserBadge($login, function ($userIdentifier): ?User {
                $user = $this->userRepository->findOneByUsernameOrEmail($userIdentifier);

                if (false === $user->isEnabled()) {
                    throw new CustomUserMessageAuthenticationException('error.user_not_enabled');
                }

                return $user;
            }),
            new PasswordCredentials($password),
            [new RememberMeBadge(), new CsrfTokenBadge('authenticate', $csrfToken)]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('app_homepage'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('app_security_login'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->router->generate('app_security_login');
    }
}
