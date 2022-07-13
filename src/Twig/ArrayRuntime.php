<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\NaturalSorter;
use Twig\Extension\RuntimeExtensionInterface;

class ArrayRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly NaturalSorter $naturalSorter)
    {}

    public function naturalSorting(iterable $array): array
    {
        return $this->naturalSorter->sort($array);
    }
}
