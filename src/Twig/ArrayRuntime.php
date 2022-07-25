<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\SortingDirectionEnum;
use App\Service\ArraySorter;
use Twig\Extension\RuntimeExtensionInterface;

class ArrayRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ArraySorter $arraySorter
    ) {
    }

    public function naturalSorting(
        iterable $array,
        ?string $direction = SortingDirectionEnum::ASCENDING,
        ?string $type = null
    ): array {
        return $this->arraySorter->sort($array, $direction, $type);
    }
}
