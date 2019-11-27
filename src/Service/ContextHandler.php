<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ContextHandler
 *
 * @package App\Service
 */
class ContextHandler
{
    /**
     * @var \Twig_Environment
     */
    private $environment;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     * Possible values are :
     * admin: Admin pages
     * user: Public pages
     * preview: Preview pages
     * default: everything else
     */
    private $context;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $username;

    /**
     * ContextHandler constructor.
     * @param \Twig_Environment $environment
     * @param RouterInterface $router
     * @param TokenStorageInterface $tokenStorage
     * @param EntityManagerInterface $em
     */
    public function __construct(\Twig_Environment $environment, RouterInterface $router, TokenStorageInterface $tokenStorage, EntityManagerInterface $em)
    {
        $this->environment = $environment;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    public function init(Request $request)
    {
        preg_match("/^\/(\w+)/", $request->getRequestUri(), $matches);

        if (isset($matches[1]) && \in_array($matches[1], ['user', 'preview', 'admin'])) {
            $this->context = $matches[1];
        } else {
            $this->context = 'default';
        }

        $this->environment->addGlobal('context', $this->context);

        if ($this->context === 'user') {
            preg_match("/^\/user\/(\w+)/", $request->getRequestUri(), $matches);
            $this->username = $matches[1];
            $this->router->getContext()->setParameter('username',$this->username);
        }
    }

    public function setContextUser()
    {
        if ($this->context === 'user') {
            $user = $this->em->getRepository(User::class)->findOneByUsername($this->username);
            if (!$user) {
                throw new NotFoundHttpException();
            }

            $this->user = $user;
        } elseif ($this->tokenStorage->getToken() && $this->tokenStorage->getToken()->getUser() instanceof User) {
            $this->user = $this->tokenStorage->getToken()->getUser();
        }
    }

    public function getContextUser() : ?User
    {
        return $this->user;
    }

    public function getContext() : string
    {
        return $this->context;
    }
}
