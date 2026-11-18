<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

use Doctrine\Migrations\Metadata\ExecutedMigration;
use Doctrine\Migrations\Metadata\ExecutedMigrationsList;
use Doctrine\Migrations\Metadata\Storage\MetadataStorage;
use Doctrine\Migrations\Version\ExecutionResult;

final readonly class PlatformMetadataStorage implements MetadataStorage
{
    public function __construct(
        private MetadataStorage $metadataStorage,
        private PlatformMigrationsNamespace $platformMigrationsNamespace
    ) {
    }

    #[\Override]
    public function ensureInitialized(): void
    {
        $this->metadataStorage->ensureInitialized();
    }

    #[\Override]
    public function getExecutedMigrations(): ExecutedMigrationsList
    {
        return new ExecutedMigrationsList(array_filter(
            $this->metadataStorage->getExecutedMigrations()->getItems(),
            fn (ExecutedMigration $migration): bool => $this->platformMigrationsNamespace->contains((string) $migration->getVersion())
        ));
    }

    #[\Override]
    public function complete(ExecutionResult $result): void
    {
        $this->metadataStorage->complete($result);
    }

    #[\Override]
    public function reset(): void
    {
        $this->metadataStorage->reset();
    }

    public function getSql(ExecutionResult $result): iterable
    {
        return method_exists($this->metadataStorage, 'getSql') ? $this->metadataStorage->getSql($result) : [];
    }
}
