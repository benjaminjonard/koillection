<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ArrayExtension extends AbstractExtension
{
    public function getFilters() : array
    {
        return [
            new TwigFilter('naturalSorting', [ArrayRuntime::class, 'naturalSorting'])
        ];
    }
}
