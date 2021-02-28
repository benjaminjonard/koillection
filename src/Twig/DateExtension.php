<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DateExtension extends AbstractExtension
{
    public function getFilters() : array
    {
        return [
            new TwigFilter('timeAgo', [DateRuntime::class, 'timeAgo']),
            new TwigFilter('timeDiff', [DateRuntime::class, 'timeDiff']),
            new TwigFilter('dateAgo', [DateRuntime::class, 'dateAgo'])
        ];
    }
}
