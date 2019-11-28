<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class DateExtension
 *
 * @package App\Twig
 */
class DateExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new TwigFilter('timeAgo', [DateRuntime::class, 'timeAgo']),
            new TwigFilter('timeDiff', [DateRuntime::class, 'timeDiff']),
            new TwigFilter('dateAgo', [DateRuntime::class, 'dateAgo'])
        ];
    }
}
