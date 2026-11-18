<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

final readonly class PlatformMigrationsNamespace
{
    public function __construct(
        private Connection $connection
    ) {
    }

    public function contains(string $version): bool
    {
        $namespace = match (true) {
            $this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform => 'App\\Migrations\\Mysql\\',
            $this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform => 'App\\Migrations\\Postgresql\\',
            default => null,
        };

        return null === $namespace || str_starts_with($version, $namespace);
    }
}
