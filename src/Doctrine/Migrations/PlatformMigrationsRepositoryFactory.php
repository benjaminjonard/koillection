<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\MigrationsRepository;

final readonly class PlatformMigrationsRepositoryFactory
{
    public function __invoke(DependencyFactory $dependencyFactory): MigrationsRepository
    {
        return new PlatformMigrationsRepository(
            $dependencyFactory->getMigrationRepository(),
            new PlatformMigrationsNamespace($dependencyFactory->getConnection())
        );
    }
}
