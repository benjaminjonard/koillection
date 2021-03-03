<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class ArrayRuntime implements RuntimeExtensionInterface
{
    public function naturalSorting(iterable $array) : array
    {
        $array = !\is_array($array) ? $array->toArray() : $array;

        $collator = collator_create('root');
        $collator->setAttribute(\Collator::NUMERIC_COLLATION, \Collator::ON);
        $collator->asort($array);

        return $array;
    }
}