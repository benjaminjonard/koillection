<?php

declare(strict_types=1);

namespace App\Service\Graph;

use App\Entity\Collection;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TreeBuilder
 *
 * @package App\Service\Graph
 */
class TreeBuilder
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * TreeBuilder constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Build an array representing the whole user collection.
     *
     * @return array
     */
    public function buildCollectionTree() : array
    {
        $collections = $this->em->getRepository(Collection::class)->findAllWithChildren();
        $tree = $this->createLeaf();

        $children = array_filter($collections, function(Collection $element) {
            return $element->getParent() === null;
        });

        foreach ($children as $child) {
            $tree['children'][] = $this->buildRecursively($collections, $child);
        }

        return $tree;
    }

    /**
     * @param array $collections
     * @param Collection $collection
     * @return array
     */
    private function buildRecursively(array $collections, Collection $collection) : array
    {
        $leaf = $this->createLeaf($collection);

        foreach ($collection->getChildren() as $child) {
            $leaf['children'][] = $this->buildRecursively($collections, $child);
        }

        return $leaf;
    }

    /**
     * @param Collection|null $collection
     * @return array
     */
    private function createLeaf(?Collection $collection = null) : array
    {
        $name = '';
        if ($collection instanceof Collection) {
            $title = $collection->getTitle();
            $name = strlen($title) > 21 ? substr($title, 0, 18) . '...' : $title;
        }

        return [
            'id' => $collection ? $collection->getId() : '',
            'name' => $name,
            'children' => []];
    }
}
