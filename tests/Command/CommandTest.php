<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Enum\DatumTypeEnum;
use App\Tests\Factory\CollectionFactory;
use App\Tests\Factory\DatumFactory;
use App\Tests\Factory\ItemFactory;
use App\Tests\Factory\LogFactory;
use App\Tests\Factory\UserFactory;
use App\Tests\FileTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Tools\DsnParser;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CommandTest extends KernelTestCase
{
    use Factories;
    use FileTrait;
    use ResetDatabase;

    public function test_command_clean_up_is_successful(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:clean-up');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
    }

    public function test_command_refresh_cached_values_is_successful(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:refresh-cached-values');
        $commandTester = new CommandTester($command);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
    }

    public function test_command_regenerate_logs_is_successful(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:regenerate-logs');
        $commandTester = new CommandTester($command);

        $user = UserFactory::createOne();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user]);
        ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user]);
        ItemFactory::createOne(['name' => 'Frieren #2', 'collection' => $collection, 'owner' => $user]);
        ItemFactory::createOne(['name' => 'Frieren #3', 'collection' => $collection, 'owner' => $user]);
        LogFactory::truncate();

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
        LogFactory::assert()->count(4);
    }

    public function test_command_regenerate_thumbnails_is_successful(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('app:regenerate-thumbnails');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);

        $user = UserFactory::createOne();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user]);
        $item = ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user]);
        $datum = DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 1, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Japanese title', 'value' => '葬送のフリーレン']);

        $item->setFile($this->createFile('png'));
        \Zenstruck\Foundry\Persistence\save($item);

        $datum->setFileImage($this->createFile('png'));
        \Zenstruck\Foundry\Persistence\save($datum);

        // Act
        $commandTester->execute([]);

        // Assert
        $commandTester->assertCommandIsSuccessful();
    }

    public function test_command_migrate_from_mysql_fails_when_current_database_is_not_mysql(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        if (static::getContainer()->get(Connection::class)->getDatabasePlatform() instanceof AbstractMySQLPlatform) {
            $this->markTestSkipped('The current database is MySQL or MariaDB.');
        }

        $application = new Application($kernel);
        $command = $application->find('app:migrate-from-mysql');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);

        // Act
        $commandTester->execute(['url' => 'postgresql://postgres:password@postgresql:5432/koillection']);

        // Assert
        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('The current database must be MySQL or MariaDB.', $commandTester->getDisplay());
    }

    public function test_command_migrate_from_mysql_is_successful(): void
    {
        // Arrange
        $kernel = self::bootKernel();
        $source = static::getContainer()->get(Connection::class);
        $postgresqlUrl = $_SERVER['POSTGRESQL_URL'] ?? $_ENV['POSTGRESQL_URL'] ?? null;
        if (!$source->getDatabasePlatform() instanceof AbstractMySQLPlatform || null === $postgresqlUrl) {
            $this->markTestSkipped('The current database must be MySQL or MariaDB, and POSTGRESQL_URL must be set.');
        }

        $databaseName = 'koillection_migration_test_' . ($_SERVER['TEST_TOKEN'] ?? $_ENV['TEST_TOKEN'] ?? '');
        $admin = $this->createPostgresqlConnection($postgresqlUrl, 'postgres');
        $admin->executeStatement(\sprintf('DROP DATABASE IF EXISTS %s', $databaseName));
        $admin->executeStatement(\sprintf('CREATE DATABASE %s', $databaseName));

        $application = new Application($kernel);
        $command = $application->find('app:migrate-from-mysql');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['yes']);

        $user = UserFactory::createOne();
        $collection = CollectionFactory::createOne(['title' => 'Frieren', 'owner' => $user]);
        $item = ItemFactory::createOne(['name' => 'Frieren #1', 'collection' => $collection, 'owner' => $user]);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 1, 'type' => DatumTypeEnum::TYPE_TEXT, 'label' => 'Japanese title', 'value' => '葬送のフリーレン']);
        DatumFactory::createOne(['owner' => $user, 'item' => $item, 'position' => 2, 'type' => DatumTypeEnum::TYPE_LIST, 'label' => 'Authors', 'value' => '["Abe Tsukasa","Yamada Kanehito"]']);
        $source->executeStatement("UPDATE koi_configuration SET value = 'Frieren collection' WHERE label = 'app-name'");
        $source->executeStatement("INSERT INTO doctrine_messenger_message (body, headers, queue_name, created_at, available_at) VALUES ('body', 'headers', 'default', '2020-04-28 00:00:00', '2020-04-28 00:00:00')");

        try {
            // Act
            $commandTester->execute(['url' => rtrim($postgresqlUrl, '/') . '/' . $databaseName]);

            // Assert
            $commandTester->assertCommandIsSuccessful();
            $target = $this->createPostgresqlConnection($postgresqlUrl, $databaseName);
            foreach (['koi_user', 'koi_collection', 'koi_item', 'koi_datum', 'doctrine_messenger_message'] as $table) {
                $this->assertSame(
                    (int) $source->fetchOne(\sprintf('SELECT COUNT(*) FROM %s', $table)),
                    (int) $target->fetchOne(\sprintf('SELECT COUNT(*) FROM %s', $table))
                );
            }

            $this->assertSame('Frieren collection', $target->fetchOne('SELECT value FROM koi_configuration WHERE label = ?', ['app-name']));
            $this->assertSame('Frieren #1', $target->fetchOne('SELECT name FROM koi_item WHERE id = ?', [$item->getId()]));
            $this->assertSame('葬送のフリーレン', $target->fetchOne('SELECT value FROM koi_datum WHERE label = ?', ['Japanese title']));
            $this->assertSame('["Abe Tsukasa","Yamada Kanehito"]', $target->fetchOne('SELECT value FROM koi_datum WHERE label = ?', ['Authors']));
            $this->assertSame(1, $target->executeStatement("INSERT INTO doctrine_messenger_message (body, headers, queue_name, created_at, available_at) VALUES ('body', 'headers', 'default', '2020-04-28 00:00:00', '2020-04-28 00:00:00')"));
            $target->close();
        } finally {
            $admin->executeStatement(\sprintf('DROP DATABASE IF EXISTS %s WITH (FORCE)', $databaseName));
        }
    }

    private function createPostgresqlConnection(string $postgresqlUrl, string $databaseName): Connection
    {
        $parser = new DsnParser(['postgresql' => 'pdo_pgsql', 'postgres' => 'pdo_pgsql', 'pgsql' => 'pdo_pgsql']);

        return DriverManager::getConnection($parser->parse(rtrim($postgresqlUrl, '/') . '/' . $databaseName));
    }
}
