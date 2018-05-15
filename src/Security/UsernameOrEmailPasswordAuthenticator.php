<?php

namespace App\Security;

use App\Entity\Connection;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use WhichBrowser\Parser;

/**
 * Class UsernameOrEmailPasswordAuthenticator
 *
 * @package App\Security
 */
class UsernameOrEmailPasswordAuthenticator implements AuthenticatorInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UsernameOrEmailPasswordAuthenticator constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param RouterInterface $router
     * @param EntityManagerInterface $em
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, RouterInterface $router, EntityManagerInterface $em)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->router = $router;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        if (!$request->request->get('login') || !$request->request->get('password')) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        return [
            'login' => $request->request->get('login'),
            'password' => $request->request->get('password'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            return $this->em->getRepository(User::class)->findOneByUsernameOrEmail($credentials['login']);
        } catch (UsernameNotFoundException $e) {
            throw new CustomUserMessageAuthenticationException('error.invalid_credentials');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            throw new CustomUserMessageAuthenticationException('error.invalid_credentials');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $parsed = new Parser($request->headers->get('User-Agent'));
        $connection = new Connection();

        $connection
            ->setUser($token->getUser())
            ->setUserAgent($request->headers->get('User-Agent'))
            ->setDate(new \Datetime())
            ->setBrowserName($parsed->browser->getName() ?: null)
            ->setBrowserVersion($parsed->browser->getVersion() ?: null)
            ->setEngineName($parsed->engine->getName() ?: null)
            ->setEngineVersion($parsed->engine->getVersion() ?: null)
            ->setOsName($parsed->os->getName() ?: null)
            ->setOsVersion($parsed->os->getVersion() ?: null)
            ->setDeviceType($parsed->device->type ?: null)
            ->setDeviceSubtype($parsed->device->subtype ?: null)
            ->setDeviceManufacturer($parsed->device->getManufacturer() ?: null)
            ->setDeviceModel($parsed->device->getModel() ?: null)
            ->setDeviceIdentifier($parsed->device->identifier ?: null)
        ;

        $this->em->persist($connection);
        $this->em->flush();

        return new RedirectResponse($this->router->generate('app_homepage'));
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->router->generate('app_security_login'));
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('app_security_login'));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
        return new PostAuthenticationGuardToken(
            $user,
            $providerKey,
            $user->getRoles()
        );
    }
}
