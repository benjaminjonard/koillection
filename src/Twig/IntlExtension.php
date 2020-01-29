<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class LogExtension
 *
 * @package App\Twig
 */
class IntlExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('getCountriesList', [IntlRuntime::class, 'getCountriesList'])
        ];
    }
}
