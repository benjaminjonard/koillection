<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use App\Service\DiskUsageCalculator;
use Twig\Extension\RuntimeExtensionInterface;

class DiskUsageRuntime implements RuntimeExtensionInterface
{
    /**
     * @var DiskUsageCalculator
     */
    private DiskUsageCalculator $diskUsageCalculator;

    public function __construct(DiskUsageCalculator $diskUsageCalculator)
    {
        $this->diskUsageCalculator = $diskUsageCalculator;
    }

    public function getSpaceUsedByUser(User $user)
    {
        return $this->diskUsageCalculator->getSpaceUsedByUser($user);
    }

    public function getSpaceUsedByUsers()
    {
        return $this->diskUsageCalculator->getSpaceUsedByUsers();
    }
}