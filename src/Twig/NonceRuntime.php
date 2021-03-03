<?php

declare(strict_types=1);

namespace App\Twig;

use App\Security\NonceGenerator;
use Twig\Extension\RuntimeExtensionInterface;

class NonceRuntime implements RuntimeExtensionInterface
{
    private NonceGenerator $nonceGenerator;

    public function __construct(NonceGenerator $nonceGenerator)
    {
        $this->nonceGenerator = $nonceGenerator;
    }

    public function getNonce() : string
    {
        return $this->nonceGenerator->getNonce();
    }
}