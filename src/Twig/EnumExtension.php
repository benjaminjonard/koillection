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
            new TwigFunction('getCurrencySymbol', static function (string $code) : ?string {
                return (new EnumRuntime())->getCurrencySymbol($code);
            }),
            new TwigFunction('getRoleLabel', static function (string $role) : string {
                return (new EnumRuntime())->getRoleLabel($role);
            }),
            new TwigFunction('getLocales', static function () : array {
                return (new EnumRuntime())->getLocales();
            }),
            new TwigFunction('getLocaleLabel', static function (string $code) : string {
                return (new EnumRuntime())->getLocaleLabel($code);
            }),
        ];
    }
}
