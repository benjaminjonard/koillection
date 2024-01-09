<?php

declare(strict_types=1);

namespace App\Enum;

use Symfony\Component\Intl\Currencies;

class CurrencyEnum
{
    public static function getCurrencyLabels(): array
    {
        $currencies = [];
        foreach (Currencies::getNames() as $code => $name) {
            if (!strpos($name, '(')) {
                $symbol = Currencies::getSymbol($code);
                $currencies[$code] = $symbol === $code ? ucwords($name) . " ({$code})" : ucwords($name) . " {$symbol} ({$code})";
            }
        }

        return $currencies;
    }

    public static function getSymbolFromCode(string $code): ?string
    {
        return Currencies::getSymbol($code);
    }
}
