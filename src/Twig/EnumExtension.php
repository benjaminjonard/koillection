<?php

namespace App\Twig;

use App\Enum\CurrencyEnum;
use App\Enum\PeriodEnum;
use App\Enum\RoleEnum;

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
        ];
    }

    /**
     * @param string $code
     * @return string
     */
    public function getCurrencySymbol(string $code) : string
    {
        return CurrencyEnum::getSymbolFromCode($code);
    }

    /**
     * @param string $code
     * @return string
     */
    public function getRoleLabel(string $role) : string
    {
        return RoleEnum::getRoleLabel($role);
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'enum_extension';
    }
}
