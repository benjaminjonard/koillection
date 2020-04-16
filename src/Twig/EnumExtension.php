<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EnumExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('getCurrencySymbol', [EnumRuntime::class, 'getCurrencySymbol']),
            new TwigFunction('getRoleLabel', [EnumRuntime::class, 'getRoleLabel']),
            new TwigFunction('getLocales', [EnumRuntime::class, 'getLocales']),
            new TwigFunction('getLocaleLabel', [EnumRuntime::class, 'getLocaleLabel']),
            new TwigFunction('getFullLocales', [EnumRuntime::class, 'getFullLocales']),
            new TwigFunction('getFullLocaleLabel', [EnumRuntime::class, 'getFullLocaleLabel']),
            new TwigFunction('getThemeColor', [EnumRuntime::class, 'getThemeColor']),
        ];
    }
}
