<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use App\Service\DiskUsageCalculator;
use Twig\Extension\RuntimeExtensionInterface;

class DiskUsageRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly DiskUsageCalculator $diskUsageCalculator
    ) {
    }

    public function getSpaceUsedByUser(User $user): float
    {
        return $this->diskUsageCalculator->getSpaceUsedByUser($user);
    }

    public function getSpaceUsedByUsers(): float
    {
        return $this->diskUsageCalculator->getSpaceUsedByUsers();
    }
}
