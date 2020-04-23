<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\Intl\Countries;
use Twig\Extension\RuntimeExtensionInterface;

class IntlRuntime implements RuntimeExtensionInterface
{
    /**
     * @return array
     */
    public function getCountriesList() : array
    {
        return Countries::getNames();
    }
}