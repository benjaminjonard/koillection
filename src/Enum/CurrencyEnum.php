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
    public const CURRENCY_EUR = 'EUR';
    public const CURRENCY_JPY = 'JPY';
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_GBP = 'GBP';

    public const CURRENCIES = [
        self::CURRENCY_EUR,
        self::CURRENCY_JPY,
        self::CURRENCY_USD,
        self::CURRENCY_GBP
    ];

    /**
     * @return array
     */
    public static function getCurrencyLabels() : array
    {
        $currencies = [];
        foreach (self::CURRENCIES as $code) {
            $currencies[$code] = Intl::getCurrencyBundle()->getCurrencySymbol($code).' ('.$code.')';
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
