<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class BreadcrumbExtension
 *
 * @package App\Twig
 */
class FooterExtension extends AbstractExtension
{
    /**
     * @return array
     */
    public function getFunctions() : array
    {
        return [
            new TwigFunction('renderFooter', [FooterRuntime::class, 'renderFooter'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }
}
