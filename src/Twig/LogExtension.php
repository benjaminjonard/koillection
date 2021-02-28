<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Log;
use App\Enum\LogTypeEnum;
use App\Service\Log\LoggerChain;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LogExtension extends AbstractExtension
{
    public function getFunctions() : array
    {
        return [
            new TwigFunction('getLogMessages', [LogRuntime::class, 'getLogMessages']),
        ];
    }
}
