<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class ContextHandler
{
    /**
     * Possible values are :
     * admin: Admin pages
     * shared: Shared pages
     * default: everything else.
     */
    private string $context;

    private ?User $user = null;

    // Username linked to the current page
    private string $username;

    public function __construct(
        private Environment $environment,
        private RouterInterface $router
    ) {
    }

    public function init(Request $request): void
    {
        preg_match("/^\/(\w+)/", $request->getRequestUri(), $matches);

        if (isset($matches[1]) && \in_array($matches[1], ['user', 'admin'])) {
            if ('user' === $matches[1]) {
                $this->context = 'shared';
            } else {
                $this->context = 'admin';
            }
        } else {
            $this->context = 'default';
        }

        $this->environment->addGlobal('context', $this->context);

        if ('shared' === $this->context) {
            preg_match("/^\/user\/(\w+)/", $request->getRequestUri(), $matches);
            $this->username = $matches[1];
            $this->router->getContext()->setParameter('username', $this->username);
        }
    }

    public function getRouteContext(string $route): string
    {
        if ('shared' === $this->context) {
            $route = str_replace('app_', 'app_'.$this->context.'_', $route);
        }

        return $route;
    }

    public function setContextUser(?User $user): ?User
    {
        $this->user = $user;

        return $user;
    }

    public function getContextUser(): ?User
    {
        return $this->user;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function isAuthorized(): string
    {
        return $this->isAuthorized();
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}
