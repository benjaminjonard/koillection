<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\CurrencyEnum;
use App\Enum\RoleEnum;
use App\Service\LocaleHelper;
use Twig\Extension\RuntimeExtensionInterface;

readonly class EnumRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private LocaleHelper $localeHelper
    ) {
    }

    public function getCurrencySymbol(string $code): ?string
    {
        return CurrencyEnum::getSymbolFromCode($code);
    }

    public function getRoleLabel(string $role): string
    {
        return RoleEnum::getRoleLabel($role);
    }

    public function getLocales(): array
    {
        return $this->localeHelper->getLocaleLabels();
    }

    public function getLocaleLabel(string $code): string
    {
        $this->localeHelper->getLocaleLabels();

        return $this->localeHelper->getLocaleLabels()[$code] ?? $this->localeHelper->getLocaleLabels()[$this->localeHelper->getDefaultLocale()];
    }
}
