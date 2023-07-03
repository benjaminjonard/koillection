<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Intl\Languages;

readonly class LocaleHelper
{
    public function __construct(
        private string $defaultLocale,
        private array $enabledLocales
    ) {
    }

    public function getLocaleLabels(): array
    {
        $languages = [];

        foreach ($this->enabledLocales as $locale) {
            $languages[$locale] = ucfirst(Languages::getName($locale, $locale));
        }

        return $languages;
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }
}
