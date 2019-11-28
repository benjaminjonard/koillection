<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\CurrencyEnum;
use App\Enum\LocaleEnum;
use App\Enum\RoleEnum;
use App\Enum\ThemeEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class EnumExtension
 *
 * @package App\Twig
 */
class EnumExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('getCurrencySymbol', [$this, 'getCurrencySymbol']),
            new TwigFunction('getRoleLabel', [$this, 'getRoleLabel']),
            new TwigFunction('getLocales', [$this, 'getLocales']),
            new TwigFunction('getLocaleLabel', [$this, 'getLocaleLabel']),
            new TwigFunction('getFullLocales', [$this, 'getFullLocales']),
            new TwigFunction('getFullLocaleLabel', [$this, 'getFullLocaleLabel']),
            new TwigFunction('getThemeColor', [$this, 'getThemeColor']),
        ];
    }

    /**
     * @param string $code
     * @return null|string
     */
    public function getCurrencySymbol(string $code) : ?string
    {
        return CurrencyEnum::getSymbolFromCode($code);
    }

    /**
     * @param string $role
     * @return string
     */
    public function getRoleLabel(string $role) : string
    {
        return RoleEnum::getRoleLabel($role);
    }

    /**
     * @return array
     */
    public function getLocales() : array
    {
        return LocaleEnum::getLocaleLabels();
    }

    /**
     * @param string $code
     * @return string
     */
    public function getLocaleLabel(string $code) : string
    {
        return LocaleEnum::getLocaleLabels()[$code] ?? LocaleEnum::LOCALE_EN;
    }

    /**
     * @return array
     */
    public function getFullLocales() : array
    {
        return LocaleEnum::getFullLocales();
    }

    /**
     * @param string $code
     * @return string
     */
    public function getFullLocaleLabel(string $code) : string
    {
        return LocaleEnum::getFullLocales()[$code] ?? LocaleEnum::getFullLocales()[LocaleEnum::LOCALE_EN];
    }

    /**
     * @param string $theme
     * @param string $hue
     * @return string
     */
    public function getThemeColor(string $theme, string $hue) : string
    {
        return ThemeEnum::getThemeColor($theme, $hue);
    }


    /**
     * @return string
     */
    public function getName() : string
    {
        return 'enum_extension';
    }
}
