<?php

namespace App\Service;

use App\Entity\Interfaces\BreabcrumbableInterface;
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
    private $contextHandler;

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
        if (!$entity instanceof BreabcrumbableInterface) {
            return [];
        }

        $explodedNamespace = explode('\\', \get_class($entity));
        $class = array_pop($explodedNamespace);
        $class = strtolower($class);

        $context = $this->contextHandler->getContext();

        $breadcrumb = [];
        $breadcrumbElement = new BreadcrumbElement();
        $breadcrumbElement->setType(BreadcrumbElement::TYPE_ENTITY)
            ->setLabel($entity->__toString())
            ->setRoute(\in_array($context, ['user', 'preview'], false) ? 'app_'.$context.'_'.$class : 'app_'.$class.'_show')
            ->setEntity($entity)
            ->setParams(['id' => $entity->getId()]);

        if ($context === 'user') {
            $breadcrumbElement->setParams(array_merge($breadcrumbElement->getParams(), ['username' => $entity->getOwner()->getUsername()]));
        }

        $breadcrumb[] = $breadcrumbElement;

        if (method_exists($entity, 'getParent') && $entity->getParent()) {
            $breadcrumb = array_merge($this->build($entity->getParent(), $context, $class), $breadcrumb);
        }

        if ($entity instanceof Item && $entity->getCollection()) {
            $breadcrumb = array_merge($this->build($entity->getCollection(), $context, 'collection'), $breadcrumb);
        }

        return $breadcrumb;
    }
}
