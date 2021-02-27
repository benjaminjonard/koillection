<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\Intl\Countries;
use Twig\Extension\RuntimeExtensionInterface;

class IntlRuntime implements RuntimeExtensionInterface
{
    public function getCountriesList() : array
    {
        return Countries::getNames();
    }

    public function getCountryName(string $code): string
    {
        return Countries::getName($code);
    }
}