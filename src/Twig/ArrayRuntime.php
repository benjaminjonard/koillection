<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class ArrayRuntime implements RuntimeExtensionInterface
{
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