<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Collection;
use App\Entity\Interfaces\BreadcrumbableInterface;
use App\Entity\Item;
use App\Entity\Scraper;
use App\Entity\Tag;
use App\Entity\User;
use App\Enum\ScraperTypeEnum;
use App\Model\BreadcrumbElement;

class BreadcrumbBuilder
{
    public function build(BreadcrumbableInterface $entity, $parent = null): array
    {
        $breadcrumb = [];
        $breadcrumbElement = new BreadcrumbElement();
        $breadcrumbElement->setType(BreadcrumbElement::TYPE_ENTITY)
            ->setLabel($entity->__toString())
            ->setRoute($this->guessRoute($entity))
            ->setEntity($entity)
            ->setParams(['id' => $entity->getId()]);

        $breadcrumb[] = $breadcrumbElement;

        if (method_exists($entity, 'getParent') && $entity->getParent()) {
            $breadcrumb = [...$this->build($entity->getParent()), ...$breadcrumb];
        }

        if ($entity instanceof Item) {
            if ($parent instanceof Tag) {
                $breadcrumb = array_merge($this->build($parent), $breadcrumb);
            } elseif ($entity->getCollection() instanceof Collection) {
                $breadcrumb = array_merge($this->build($entity->getCollection()), $breadcrumb);
            }
        }

        return $breadcrumb;
    }

    private function guessRoute(BreadcrumbableInterface $entity): string
    {
        $explodedNamespace = explode('\\', $entity::class);
        $class = array_pop($explodedNamespace);
        $pieces = preg_split('/(?=[A-Z])/', lcfirst($class));
        $class = implode('_', $pieces);
        $class = strtolower($class);

        return match (true) {
            $entity instanceof User => 'app_admin_user_index',
            $entity instanceof Scraper && $entity->getType() === ScraperTypeEnum::TYPE_ITEM => 'app_scraper_item_index',
            $entity instanceof Scraper && $entity->getType() === ScraperTypeEnum::TYPE_COLLECTION => 'app_scraper_collection_index',
            $entity instanceof Scraper && $entity->getType() === ScraperTypeEnum::TYPE_WISH => 'app_scraper_wish_index',
            default => 'app_' . $class . '_show',
        };
    }
}
