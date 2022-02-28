<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('safeContent', [AppRuntime::class, 'safeContent'], ['is_safe' => ['html']]),
            new TwigFilter('bytes', [AppRuntime::class, 'bytes']),
            new TwigFilter('highlightTags', [AppRuntime::class, 'highlightTags'], ['is_safe' => ['html']])
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('renderTitle', [AppRuntime::class, 'renderTitle']),
            new TwigFunction('getUnderlinedTags', [AppRuntime::class, 'getUnderlinedTags'], ['is_safe' => ['html']]),
            new TwigFunction('isFeatureEnabled', [AppRuntime::class, 'isFeatureEnabled']),
            new TwigFunction('createDeleteForm', [AppRuntime::class, 'createDeleteForm'], ['is_safe' => ['html']])
        ];
    }
}
