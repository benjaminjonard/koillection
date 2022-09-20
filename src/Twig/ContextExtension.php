<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ContextExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('applyContext', [ContextRuntime::class, 'applyContext']),
            new TwigFilter('applyContextTrans', [ContextRuntime::class, 'applyContextTrans']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getContextUser', [ContextRuntime::class, 'getContextUser'])
        ];
    }
}
