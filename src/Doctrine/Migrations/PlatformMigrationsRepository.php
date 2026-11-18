<?php

declare(strict_types=1);

namespace App\Doctrine\Migrations;

use Doctrine\Migrations\Exception\MigrationClassNotFound;
use Doctrine\Migrations\Metadata\AvailableMigration;
use Doctrine\Migrations\Metadata\AvailableMigrationsSet;
use Doctrine\Migrations\MigrationsRepository;
use Doctrine\Migrations\Version\Version;

final readonly class PlatformMigrationsRepository implements MigrationsRepository
{
    public function __construct(
        private MigrationsRepository $migrationsRepository,
        private PlatformMigrationsNamespace $platformMigrationsNamespace
    ) {
    }

    #[\Override]
    public function hasMigration(string $version): bool
    {
        return $this->platformMigrationsNamespace->contains($version) && $this->migrationsRepository->hasMigration($version);
    }

    #[\Override]
    public function getMigration(Version $version): AvailableMigration
    {
        if (!$this->platformMigrationsNamespace->contains((string) $version)) {
            throw MigrationClassNotFound::new((string) $version);
        }

        return $this->migrationsRepository->getMigration($version);
    }

    #[\Override]
    public function getMigrations(): AvailableMigrationsSet
    {
        return new AvailableMigrationsSet(array_filter(
            $this->migrationsRepository->getMigrations()->getItems(),
            fn (AvailableMigration $migration): bool => $this->platformMigrationsNamespace->contains((string) $migration->getVersion())
        ));
    }
}
