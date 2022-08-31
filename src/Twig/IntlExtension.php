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
            new TwigFunction('getCountriesList', function () : array {
                return (new IntlRuntime())->getCountriesList();
            }),
            new TwigFunction('getCountryName', function (string $code) : string {
                return (new IntlRuntime())->getCountryName($code);
            }),
            new TwigFunction('getCountryFlag', function (string $code) : string {
                return (new IntlRuntime())->getCountryFlag($code);
            }),
        ];
    }
}
