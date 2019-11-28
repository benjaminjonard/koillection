<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class CountersExtension
 *
 * @package App\Twig
 */
class CountersExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('getCounters', [CountersRuntime::class, 'getCounters']),
            new TwigFunction('getTotalItemsCounter', [CountersRuntime::class, 'getTotalItemsCounter']),
            new TwigFunction('getTotalChildrenCounter', [CountersRuntime::class, 'getTotalChildrenCounter']),
        ];
    }
}
