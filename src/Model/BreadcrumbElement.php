<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Class BreadcrumbElement
 *
 * @package App\Model
 */
class BreadcrumbElement
{
    public const TYPE_ROOT = 'root';
    public const TYPE_ENTITY = 'entity';
    public const TYPE_ACTION = 'action';

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $route;

    /**
     * @var object
     */
    private $entity;

    /**
     * @var array
     */
    private $params;

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return BreadcrumbElement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label.
     *
     * @param string $label
     *
     * @return BreadcrumbElement
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get route.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set route.
     *
     * @param string $route
     *
     * @return BreadcrumbElement
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get class.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set class.
     *
     * @param string $class
     *
     * @return BreadcrumbElement
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get params.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set params.
     *
     * @param array $params
     *
     * @return BreadcrumbElement
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get entity.
     *
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set entity.
     *
     * @param object $entity
     *
     * @return BreadcrumbElement
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }
}
