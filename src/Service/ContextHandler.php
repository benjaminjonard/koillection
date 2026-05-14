<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\ResetInterface;
use Twig\Environment;

class ContextHandler implements ResetInterface
{
    /**
     * Possible values are :
     * admin: Admin pages
     * shared: Shared pages
     * default: everything else.
     */
    private string $context = 'default';

    private ?User $user = null;

    // Username linked to the current page
    private ?string $username = null;

    public function __construct(
        private readonly Environment $environment,
        private readonly RouterInterface $router
    ) {
    }

    public function init(Request $request): void
    {
        preg_match("/^\/(\w+)/", $request->getRequestUri(), $matches);

        if (isset($matches[1]) && \in_array($matches[1], ['user', 'admin'])) {
            $this->context = 'user' === $matches[1] ? 'shared' : 'admin';
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

    public function reset(): void
    {
        $this->context = 'default';
        $this->user = null;
        $this->username = null;
        $this->router->getContext()->setParameter('username', null);
    }

    public function getRouteContext(string $route): string
    {
        if ('shared' === $this->context) {
            $route = str_replace('app_', 'app_' . $this->context . '_', $route);
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

    public function getUsername(): ?string
    {
        return $this->username;
    }
}
