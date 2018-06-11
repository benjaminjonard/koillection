<?php

namespace App\Twig;

use App\Enum\CurrencyEnum;
use App\Enum\LocaleEnum;
use App\Enum\RoleEnum;
use App\Enum\ThemeEnum;

/**
 * Class EnumExtension
 *
 * @package App\Twig
 */
class EnumExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new \Twig_SimpleFunction('getCurrencySymbol', [$this, 'getCurrencySymbol']),
            new \Twig_SimpleFunction('getRoleLabel', [$this, 'getRoleLabel']),
            new \Twig_SimpleFunction('getLocales', [$this, 'getLocales']),
            new \Twig_SimpleFunction('getLocaleLabel', [$this, 'getLocaleLabel']),
            new \Twig_SimpleFunction('getThemeColor', [$this, 'getThemeColor']),
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
     * @return array
     */
    public function getLocaleLabel(string $code) : string
    {
        return LocaleEnum::getLocaleLabels()[$code] ?? LocaleEnum::LOCALE_EN;
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
