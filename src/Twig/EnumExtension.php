<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EnumExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getCurrencySymbol', function (string $code) : ?string {
                return (new EnumRuntime())->getCurrencySymbol($code);
            }),
            new TwigFunction('getRoleLabel', function (string $role) : string {
                return (new EnumRuntime())->getRoleLabel($role);
            }),
            new TwigFunction('getLocales', function () : array {
                return (new EnumRuntime())->getLocales();
            }),
            new TwigFunction('getLocaleLabel', function (string $code) : string {
                return (new EnumRuntime())->getLocaleLabel($code);
            }),
        ];
    }
}
