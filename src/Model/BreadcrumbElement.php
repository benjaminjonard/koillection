<?php

declare(strict_types=1);

namespace App\Model;

use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\User;

class BreadcrumbElement
{
    public const TYPE_ROOT = 'root';

    public const TYPE_ENTITY = 'entity';

    public const TYPE_ACTION = 'action';

    private string $type;

    private string $label;

    private ?string $class = null;

    private string $route;

    private ?object $entity = null;

    private array $params;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function getClass(): string|null
    {
        return $this->class;
    }

    public function setClass(string $class): static
    {
        $this->class = $class;

        return $this;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams(array $params): static
    {
        $this->params = $params;

        return $this;
    }

    public function getEntity(): object|null
    {
        return $this->entity;
    }

    public function setEntity(User|BreadcrumbableInterface $entity): static
    {
        $this->entity = $entity;

        return $this;
    }
}
