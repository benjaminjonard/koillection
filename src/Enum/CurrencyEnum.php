<?php

declare(strict_types=1);

namespace App\Enum;

use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Currencies;

class CurrencyEnum
{
    /**
     * @return array
     */
    public static function getCurrencyLabels() : array
    {
        $currencies = [];
        foreach (Currencies::getNames() as $code => $name) {
            if (!strpos($name, '(')) {
                $symbol = Currencies::getSymbol($code);
                if ($symbol === $code) {
                    $currencies[$code] = ucwords($name) . " ($code)";
                } else {
                    $currencies[$code] = ucwords($name) . " $symbol ($code)";
                }
            }
        }

        return $currencies;
    }

    /**
     * @param string $code
     * @return null|string
     */
    public static function getSymbolFromCode(string $code) : ?string
    {
        return Currencies::getSymbol($code);
    }
}
