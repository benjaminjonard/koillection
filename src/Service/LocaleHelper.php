<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Intl\Locales;

readonly class LocaleHelper
{
    public function __construct(
        #[Autowire('%default_locale%')] private string $defaultLocale,
        #[Autowire('%kernel.enabled_locales%')] private array $enabledLocales
    ) {
    }

    public function getLocaleLabels(): array
    {
        $languages = [];

        foreach ($this->enabledLocales as $locale) {
            $languages[$locale] = ucfirst(Locales::getName($locale, $locale));
        }

        asort($languages);

        return $languages;
    }

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }
}
