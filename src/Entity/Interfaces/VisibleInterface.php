<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

interface VisibleInterface
{
    public function updateDescendantsVisibility(): self;
}
