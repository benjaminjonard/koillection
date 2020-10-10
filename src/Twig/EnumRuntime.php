<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\CurrencyEnum;
use App\Enum\LocaleEnum;
use App\Enum\RoleEnum;
use Twig\Extension\RuntimeExtensionInterface;

class EnumRuntime implements RuntimeExtensionInterface
{
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
        return LocaleEnum::getLocaleLabels()[$code] ?? LocaleEnum::getLocaleLabels()[LocaleEnum::LOCALE_EN_GB];
    }
}