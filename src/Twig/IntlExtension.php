<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IntlExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getCountriesList', [IntlRuntime::class, 'getCountriesList']),
            new TwigFunction('getCountryName', [IntlRuntime::class, 'getCountryName']),
            new TwigFunction('getCountryFlag', [IntlRuntime::class, 'getCountryFlag'])
        ];
    }
}
