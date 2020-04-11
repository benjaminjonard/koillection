<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Item;
use App\Entity\Wishlist;
use App\Model\BreadcrumbElement;

/**
 * Class BreadcrumbBuilder
 *
 * @package App\Service
 */
class BreadcrumbBuilder
{
    /**
     * @var ContextHandler
     */
    private ContextHandler $contextHandler;

    /**
     * BreadcrumbBuilder constructor.
     * @param ContextHandler $contextHandler
     */
    public function __construct(ContextHandler $contextHandler)
    {
        $this->contextHandler = $contextHandler;
    }

    /**
     * @param $entity
     * @return array
     */
    public function build($entity) :array
    {
        if (!$entity instanceof BreadcrumbableInterface) {
            return [];
        }

        $explodedNamespace = explode('\\', \get_class($entity));
        $class = \array_pop($explodedNamespace);
        $pieces = preg_split('/(?=[A-Z])/', lcfirst($class));
        $class = implode('_', $pieces);
        $class = strtolower($class);

        $breadcrumb = [];
        $breadcrumbElement = new BreadcrumbElement();
        $breadcrumbElement->setType(BreadcrumbElement::TYPE_ENTITY)
            ->setLabel($entity->__toString())
            ->setRoute('app_'.$class.'_show')
            ->setEntity($entity)
            ->setParams(['id' => $entity->getId()]);

        $breadcrumb[] = $breadcrumbElement;

        if (method_exists($entity, 'getParent') && $entity->getParent()) {
            $breadcrumb = \array_merge($this->build($entity->getParent()), $breadcrumb);
        }

        if ($entity instanceof Item && $entity->getCollection()) {
            $breadcrumb = \array_merge($this->build($entity->getCollection()), $breadcrumb);
        }

        return $breadcrumb;
    }
}
