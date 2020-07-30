<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new TwigFilter('safeContent', [AppRuntime::class, 'safeContent'], ['is_safe' => ['html']]),
            new TwigFilter('bytes', [AppRuntime::class, 'bytes']),
            new TwigFilter('highlightTags', [AppRuntime::class, 'highlightTags'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('renderTitle', [AppRuntime::class, 'renderTitle']),
            new TwigFunction('getUnderlinedTags', [AppRuntime::class, 'getUnderlinedTags'], ['is_safe' => ['html']])
        ];
    }
}
