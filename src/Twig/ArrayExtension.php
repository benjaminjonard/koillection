<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ArrayExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new TwigFilter('add', [ArrayRuntime::class, 'add']),
            new TwigFilter('remove', [ArrayRuntime::class, 'remove']),
            new TwigFilter('reorder', [ArrayRuntime::class, 'reorder']),
            new TwigFilter('naturalSorting', [ArrayRuntime::class, 'naturalSorting']),
        ];
    }
}
