<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class ContextExtension
 *
 * @package App\Twig
 */
class ContextExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFilters() : array
    {
        return [
            new TwigFilter('applyContext', [ContextRuntime::class, 'applyContext']),
            new TwigFilter('applyContextTrans', [ContextRuntime::class, 'applyContextTrans'])
        ];
    }
}
