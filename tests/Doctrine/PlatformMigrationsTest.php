<?php

declare(strict_types=1);

namespace App\Tests\App\Doctrine;

use App\Doctrine\Migrations\PlatformMetadataStorage;
use App\Doctrine\Migrations\PlatformMigrationsNamespace;
use App\Doctrine\Migrations\PlatformMigrationsRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Exception\MigrationClassNotFound;
use Doctrine\Migrations\Metadata\AvailableMigration;
use Doctrine\Migrations\Metadata\AvailableMigrationsSet;
use Doctrine\Migrations\Metadata\ExecutedMigration;
use Doctrine\Migrations\Metadata\ExecutedMigrationsList;
use Doctrine\Migrations\Metadata\Storage\MetadataStorage;
use Doctrine\Migrations\MigrationsRepository;
use Doctrine\Migrations\Version\Version;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PlatformMigrationsTest extends KernelTestCase
{
    private const string MYSQL_VERSION = 'App\Migrations\Mysql\Version20261118221036';

    private const string POSTGRESQL_VERSION = 'App\Migrations\Postgresql\Version20261118220628';

    public static function platformProvider(): \Iterator
    {
        yield 'mysql' => [new MySQLPlatform(), true, false];
        yield 'mariadb' => [new MariaDBPlatform(), true, false];
        yield 'postgresql' => [new PostgreSQLPlatform(), false, true];
        yield 'other' => [new SQLitePlatform(), true, true];
    }

    #[DataProvider('platformProvider')]
    public function test_namespace_contains_only_versions_of_the_current_platform(AbstractPlatform $platform, bool $containsMysql, bool $containsPostgresql): void
    {
        $platformMigrationsNamespace = $this->createPlatformMigrationsNamespace($platform);

        $this->assertSame($containsMysql, $platformMigrationsNamespace->contains(self::MYSQL_VERSION));
        $this->assertSame($containsPostgresql, $platformMigrationsNamespace->contains(self::POSTGRESQL_VERSION));
    }

    public function test_repository_get_migrations_returns_only_migrations_of_the_current_platform(): void
    {
        $repository = $this->createRepository();

        $versions = array_map(
            static fn (AvailableMigration $migration): string => (string) $migration->getVersion(),
            $repository->getMigrations()->getItems()
        );

        $this->assertSame([self::POSTGRESQL_VERSION], $versions);
    }

    public function test_repository_has_migration_ignores_migrations_of_other_platforms(): void
    {
        $repository = $this->createRepository();

        $this->assertTrue($repository->hasMigration(self::POSTGRESQL_VERSION));
        $this->assertFalse($repository->hasMigration(self::MYSQL_VERSION));
    }

    public function test_repository_get_migration_returns_migration_of_the_current_platform(): void
    {
        $repository = $this->createRepository();

        $migration = $repository->getMigration(new Version(self::POSTGRESQL_VERSION));

        $this->assertSame(self::POSTGRESQL_VERSION, (string) $migration->getVersion());
    }

    public function test_repository_get_migration_throws_for_migrations_of_other_platforms(): void
    {
        $repository = $this->createRepository();

        $this->expectException(MigrationClassNotFound::class);

        $repository->getMigration(new Version(self::MYSQL_VERSION));
    }

    public function test_metadata_storage_returns_only_executed_migrations_of_the_current_platform(): void
    {
        $metadataStorage = $this->createStub(MetadataStorage::class);
        $metadataStorage->method('getExecutedMigrations')->willReturn(new ExecutedMigrationsList([
            new ExecutedMigration(new Version(self::MYSQL_VERSION)),
            new ExecutedMigration(new Version(self::POSTGRESQL_VERSION)),
        ]));

        $platformMetadataStorage = new PlatformMetadataStorage($metadataStorage, $this->createPlatformMigrationsNamespace(new MySQLPlatform()));

        $versions = array_map(
            static fn (ExecutedMigration $migration): string => (string) $migration->getVersion(),
            $platformMetadataStorage->getExecutedMigrations()->getItems()
        );

        $this->assertSame([self::MYSQL_VERSION], $versions);
    }

    public function test_migrations_configuration_only_returns_migrations_of_the_current_platform(): void
    {
        $dependencyFactory = static::getContainer()->get('doctrine.migrations.dependency_factory');
        $this->assertInstanceOf(DependencyFactory::class, $dependencyFactory);

        $platformMigrationsNamespace = new PlatformMigrationsNamespace($dependencyFactory->getConnection());
        $migrations = $dependencyFactory->getMigrationRepository()->getMigrations()->getItems();

        $this->assertNotEmpty($migrations);
        foreach ($migrations as $migration) {
            $this->assertInstanceOf(AvailableMigration::class, $migration);
            $this->assertTrue($platformMigrationsNamespace->contains((string) $migration->getVersion()));
        }
    }

    private function createPlatformMigrationsNamespace(AbstractPlatform $platform): PlatformMigrationsNamespace
    {
        $connection = $this->createStub(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn($platform);

        return new PlatformMigrationsNamespace($connection);
    }

    private function createRepository(): PlatformMigrationsRepository
    {
        $mysqlMigration = new AvailableMigration(new Version(self::MYSQL_VERSION), $this->createStub(AbstractMigration::class));
        $postgresqlMigration = new AvailableMigration(new Version(self::POSTGRESQL_VERSION), $this->createStub(AbstractMigration::class));

        $migrationsRepository = $this->createStub(MigrationsRepository::class);
        $migrationsRepository->method('getMigrations')->willReturn(new AvailableMigrationsSet([$mysqlMigration, $postgresqlMigration]));
        $migrationsRepository->method('hasMigration')->willReturn(true);
        $migrationsRepository->method('getMigration')->willReturnCallback(
            static fn (Version $version): AvailableMigration => (string) $version === self::MYSQL_VERSION ? $mysqlMigration : $postgresqlMigration
        );

        return new PlatformMigrationsRepository($migrationsRepository, $this->createPlatformMigrationsNamespace(new PostgreSQLPlatform()));
    }
}
