<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class ArrayRuntime implements RuntimeExtensionInterface
{
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

        if (($key = \array_search($element, $array, false)) !== false) {
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
        return \array_values($array);
    }

    /**
     * @param $collection
     * @return array
     */
    public function naturalSorting($collection) : array
    {
        $array = !\is_array($collection) ? $collection->toArray() : $collection;

        $collator = collator_create('root');
        $collator->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);
        $collator->asort($array);

        return $array;
    }
}