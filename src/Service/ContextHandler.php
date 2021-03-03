<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class ContextHandler
{
    private Environment $environment;

    private RouterInterface $router;

    /**
     * Possible values are :
     * admin: Admin pages
     * user: Public pages
     * preview: Preview pages
     * default: everything else
     */
    private string $context;

    private ?User $user;

    private string $username;

    public function __construct(Environment $environment, RouterInterface $router)
    {
        $this->environment = $environment;
        $this->router = $router;
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

    public function getRouteContext($route): string
    {
        if (\in_array($this->context, ['user', 'preview'])) {
            $route = str_replace('app_', 'app_'.$this->context.'_', $route);
        }

        return $route;
    }

    public function setContextUser(?User $user): ?User
    {
        $this->user = $user;

        return $user;
    }

    public function getContextUser() : ?User
    {
        return $this->user;
    }

    public function getContext() : string
    {
        return $this->context;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}
