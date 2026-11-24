<?php

declare(strict_types=1);

namespace App\Command;

use App\Doctrine\Migrations\PlatformMetadataStorageFactory;
use App\Doctrine\Migrations\PlatformMigrationsRepositoryFactory;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\DBAL\TransactionIsolationLevel;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ExistingConfiguration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\Storage\MetadataStorage;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\Migrations\MigrationsRepository;
use Doctrine\Migrations\MigratorConfiguration;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'app:migrate-from-mysql',
    description: 'Copy all data from the current MySQL or MariaDB database to an empty PostgreSQL database',
)]
class MigrateFromMysqlCommand extends Command
{
    private const int BATCH_SIZE = 500;

    public function __construct(
        private readonly Connection $connection,
        #[Autowire(service: 'doctrine.migrations.dependency_factory')] private readonly DependencyFactory $dependencyFactory,
        #[Autowire(service: 'doctrine.migrations.configuration')] private readonly Configuration $migrationsConfiguration
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'URL of the empty PostgreSQL database, for example postgresql://user:password@postgresql:5432/koillection');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Please do a backup of your MySQL database before continuing. Are you sure you want to continue ? (y/N)', false);

        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        if (!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform) {
            $output->writeln('<error>The current database must be MySQL or MariaDB.</error>');

            return Command::FAILURE;
        }

        if (\count($this->dependencyFactory->getMigrationStatusCalculator()->getNewMigrations()) > 0) {
            $output->writeln('<error>The MySQL database is not up to date. Run all migrations first.</error>');

            return Command::FAILURE;
        }

        $parser = new DsnParser(['postgresql' => 'pdo_pgsql', 'postgres' => 'pdo_pgsql', 'pgsql' => 'pdo_pgsql']);
        $target = DriverManager::getConnection($parser->parse($input->getArgument('url')));

        if (!$target->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            $output->writeln('<error>The target database must be PostgreSQL.</error>');

            return Command::FAILURE;
        }

        if ($target->createSchemaManager()->introspectTableNames() !== []) {
            $output->writeln('<error>The PostgreSQL database must be empty.</error>');

            return Command::FAILURE;
        }

        $output->writeln('Creating the PostgreSQL schema...');
        $this->createSchema($target);

        $output->writeln('Copying data...');
        $metadataStorageConfiguration = $this->migrationsConfiguration->getMetadataStorageConfiguration();
        $migrationsTableName = $metadataStorageConfiguration instanceof TableMetadataStorageConfiguration ? $metadataStorageConfiguration->getTableName() : null;
        $tables = array_values(array_filter(
            $target->createSchemaManager()->introspectTables(),
            static fn (Table $table): bool => $table->getObjectName()->getUnqualifiedName()->getValue() !== $migrationsTableName
        ));

        $nativeConnection = $this->connection->getNativeConnection();
        $nativeConnection->setAttribute(\Pdo\Mysql::ATTR_USE_BUFFERED_QUERY, false);
        $this->connection->setTransactionIsolation(TransactionIsolationLevel::REPEATABLE_READ);
        $this->connection->beginTransaction();
        $target->beginTransaction();

        try {
            foreach ($tables as $table) {
                $this->dropForeignKeys($target, $table);
            }

            foreach ($tables as $table) {
                $target->executeStatement($target->getDatabasePlatform()->getTruncateTableSQL($table->getObjectName()->toSQL($target->getDatabasePlatform())));
            }

            foreach ($tables as $table) {
                $count = $this->copyTable($target, $table);
                $output->writeln(\sprintf('%s: %d rows', $table->getObjectName()->getUnqualifiedName()->getValue(), $count));
            }

            foreach ($tables as $table) {
                $this->resetSequences($target, $table);
                $this->createForeignKeys($target, $table);
            }

            $target->commit();
            $this->connection->commit();
        } catch (\Throwable $throwable) {
            $target->rollBack();
            $this->connection->rollBack();
            $output->writeln('<error>The migration failed, no data was copied. Drop and recreate the PostgreSQL database before running this command again.</error>');

            throw $throwable;
        } finally {
            $nativeConnection->setAttribute(\Pdo\Mysql::ATTR_USE_BUFFERED_QUERY, true);
        }

        $output->writeln('Done! Change the DB_* environment variables to use the PostgreSQL database, then restart Koillection.');

        return Command::SUCCESS;
    }

    private function createSchema(Connection $target): void
    {
        $dependencyFactory = DependencyFactory::fromConnection(
            new ExistingConfiguration($this->migrationsConfiguration),
            new ExistingConnection($target)
        );
        $dependencyFactory->setDefinition(MigrationsRepository::class, new PlatformMigrationsRepositoryFactory());
        $dependencyFactory->setDefinition(MetadataStorage::class, new PlatformMetadataStorageFactory());

        $dependencyFactory->getMetadataStorage()->ensureInitialized();
        $version = $dependencyFactory->getVersionAliasResolver()->resolveVersionAlias('latest');
        $plan = $dependencyFactory->getMigrationPlanCalculator()->getPlanUntilVersion($version);
        $dependencyFactory->getMigrator()->migrate($plan, new MigratorConfiguration());
    }

    private function dropForeignKeys(Connection $target, Table $table): void
    {
        $platform = $target->getDatabasePlatform();
        foreach ($table->getForeignKeys() as $foreignKey) {
            $target->executeStatement($platform->getDropForeignKeySQL($foreignKey->getObjectName()->toSQL($platform), $table->getObjectName()->toSQL($platform)));
        }
    }

    private function createForeignKeys(Connection $target, Table $table): void
    {
        $platform = $target->getDatabasePlatform();
        foreach ($table->getForeignKeys() as $foreignKey) {
            $target->executeStatement($platform->getCreateForeignKeySQL($foreignKey, $table->getObjectName()->toSQL($platform)));
        }
    }

    private function copyTable(Connection $target, Table $table): int
    {
        $sourcePlatform = $this->connection->getDatabasePlatform();
        $targetPlatform = $target->getDatabasePlatform();
        $tableName = $table->getObjectName()->getUnqualifiedName()->getValue();
        $columns = $table->getColumns();
        $columnNames = array_map(static fn (Column $column): string => $column->getObjectName()->getIdentifier()->getValue(), $columns);

        $result = $this->connection->executeQuery(\sprintf(
            'SELECT %s FROM %s',
            implode(', ', array_map($sourcePlatform->quoteSingleIdentifier(...), $columnNames)),
            $sourcePlatform->quoteSingleIdentifier($tableName)
        ));

        $insert = \sprintf(
            'INSERT INTO %s (%s) VALUES ',
            $targetPlatform->quoteSingleIdentifier($tableName),
            implode(', ', array_map($targetPlatform->quoteSingleIdentifier(...), $columnNames))
        );
        $placeholders = '(' . implode(', ', array_fill(0, \count($columns), '?')) . ')';

        $count = 0;
        $params = [];
        $types = [];
        $rowsInBatch = 0;
        while (false !== ($row = $result->fetchNumeric())) {
            foreach ($columns as $index => $column) {
                $type = $column->getType();
                $params[] = $type->convertToDatabaseValue($type->convertToPHPValue($row[$index], $sourcePlatform), $targetPlatform);
                $types[] = $type->getBindingType();
            }

            ++$rowsInBatch;
            ++$count;

            if ($rowsInBatch === self::BATCH_SIZE) {
                $target->executeStatement($insert . implode(', ', array_fill(0, $rowsInBatch, $placeholders)), $params, $types);
                $params = [];
                $types = [];
                $rowsInBatch = 0;
            }
        }

        if ($rowsInBatch > 0) {
            $target->executeStatement($insert . implode(', ', array_fill(0, $rowsInBatch, $placeholders)), $params, $types);
        }

        $targetCount = (int) $target->fetchOne(\sprintf('SELECT COUNT(*) FROM %s', $targetPlatform->quoteSingleIdentifier($tableName)));
        if ($targetCount !== $count) {
            throw new \RuntimeException(\sprintf('Table %s: %d rows read from MySQL, %d rows found in PostgreSQL.', $tableName, $count, $targetCount));
        }

        return $count;
    }

    private function resetSequences(Connection $target, Table $table): void
    {
        $platform = $target->getDatabasePlatform();
        $tableName = $table->getObjectName()->getUnqualifiedName()->getValue();
        foreach ($table->getColumns() as $column) {
            if (!$column->getAutoincrement()) {
                continue;
            }

            $columnName = $column->getObjectName()->getIdentifier()->getValue();
            $target->executeStatement(\sprintf(
                'SELECT setval(pg_get_serial_sequence(%s, %s), COALESCE(MAX(%s), 0) + 1, false) FROM %s',
                $platform->quoteStringLiteral($platform->quoteSingleIdentifier($tableName)),
                $platform->quoteStringLiteral($columnName),
                $platform->quoteSingleIdentifier($columnName),
                $platform->quoteSingleIdentifier($tableName)
            ));
        }
    }
}
