<?php

declare(strict_types=1);

namespace App\Enum;

use Symfony\Component\Intl\Intl;

/**
 * Class CurrencyEnum
 *
 * @package App\Enum
 */
class CurrencyEnum
{
    /**
     * @return array
     */
    public static function getCurrencyLabels() : array
    {
        $currencies = [];
        foreach (Intl::getCurrencyBundle()->getCurrencyNames() as $code => $name) {
            if (!strpos($name, '(')) {
                $symbol = Intl::getCurrencyBundle()->getCurrencySymbol($code);
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
        return Intl::getCurrencyBundle()->getCurrencySymbol($code);
    }
}
