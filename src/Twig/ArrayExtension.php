<?php

namespace App\Twig;

use App\Entity\Item;

/**
 * Class ArrayExtension
 *
 * @package App\Twig
 */
class ArrayExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('add', [$this, 'add']),
            new \Twig_SimpleFilter('remove', [$this, 'remove']),
            new \Twig_SimpleFilter('reorder', [$this, 'reorder']),
            new \Twig_SimpleFilter('naturalSorting', [$this, 'naturalSorting']),
        ];
    }

    /**
     * @param array|null $array
     * @param string $element
     * @return array
     */
    public function add(?array $array, string $element) : array
    {
        if (!\is_array($array)) {
            $array = [];
        }

        if (!\in_array($element, $array, false)) {
            $array[] = $element;
        }

        return $array;
    }

    /**
     * @param array|null $array
     * @param string $element
     * @return array
     */
    public function remove(?array $array, string $element) : array
    {
        if (!\is_array($array)) {
            $array = [];
        }

        if (($key = array_search($element, $array, false)) !== false) {
            unset($array[$key]);
        }

        return $array;
    }

    /**
     * @param array $array
     * @return array
     */
    public function reorder(array $array) : array
    {
        return array_values($array);
    }

    /**
     * @param $collection
     * @return array
     */
    public function naturalSorting($collection) : array
    {
        $array = !\is_array($collection) ? $collection->toArray() : $collection;

        usort($array, function (Item $a, Item $b) {
            return strnatcmp($a->getName(), $b->getName());
        });

        return $array;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'array_extension';
    }
}
