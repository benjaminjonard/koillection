<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class NonceExtension
 *
 * @package App\Twig
 */
class NonceExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('csp_nonce', [NonceRuntime::class, 'getNonce']),
        ];
    }
}
