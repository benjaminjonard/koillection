<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class UsernameOrEmailPasswordAuthenticator implements AuthenticatorInterface
{
    private UserPasswordEncoderInterface $passwordEncoder;

    private RouterInterface $router;

    private EntityManagerInterface $em;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, RouterInterface $router, EntityManagerInterface $em)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->router = $router;
        $this->em = $em;
    }

    public function supports(Request $request): bool
    {
        if (!$request->request->get('_login') || !$request->request->get('_password')) {
            return false;
        }

        return true;
    }

    public function getCredentials(Request $request): array
    {
        return [
            'login' => $request->request->get('_login'),
            'password' => $request->request->get('_password'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        try {
            return $this->em->getRepository(User::class)->findOneByUsernameOrEmail($credentials['login']);
        } catch (UsernameNotFoundException $e) {
            throw new CustomUserMessageAuthenticationException('error.invalid_credentials');
        }
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if ($user->isEnabled() === false) {
            throw new CustomUserMessageAuthenticationException('error.user_not_enabled');
        }

        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            throw new CustomUserMessageAuthenticationException('error.invalid_credentials');
        }

        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('app_homepage'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): RedirectResponse
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->router->generate('app_security_login'));
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('app_security_login'));
    }

    public function supportsRememberMe(): bool
    {
        return true;
    }

    public function createAuthenticatedToken(UserInterface $user, $providerKey): PostAuthenticationGuardToken
    {
        return new PostAuthenticationGuardToken(
            $user,
            $providerKey,
            $user->getRoles()
        );
    }
}
