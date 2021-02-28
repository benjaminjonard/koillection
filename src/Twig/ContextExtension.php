<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ContextExtension extends AbstractExtension
{
    public function getFilters() : array
    {
        return [
            new TwigFilter('applyContext', [ContextRuntime::class, 'applyContext']),
            new TwigFilter('applyContextTrans', [ContextRuntime::class, 'applyContextTrans'])
        ];
    }
}
