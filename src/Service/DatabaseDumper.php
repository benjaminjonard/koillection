<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class DatabaseDumper
{
    private ManagerRegistry $managerRegistry;

    private ContextHandler $contextHandler;

    private Security $security;

    private UserRepository $userRepository;

    public function __construct(ManagerRegistry $managerRegistry, Security $security, ContextHandler $contextHandler, UserRepository $userRepository)
    {
        $this->managerRegistry = $managerRegistry;
        $this->security = $security;
        $this->contextHandler = $contextHandler;
        $this->userRepository = $userRepository;
    }

    public function dump() : array
    {
        $connection = $this->managerRegistry->getManager()->getConnection();
        $rows = [];

        //Disable foreign keys
        $platformName = $this->managerRegistry->getManager()->getConnection()->getDatabasePlatform()->getName();
        $disableForeignKeysCheck = null;
        $enableForeignKeysCheck = null;
        if ($platformName === 'postgresql') {
            $disableForeignKeysCheck = 'SET session_replication_role = replica;'.PHP_EOL.PHP_EOL;
            $enableForeignKeysCheck = 'SET session_replication_role = DEFAULT;'.PHP_EOL;;
        } else if ($platformName === 'mysql') {
            $disableForeignKeysCheck = 'SET FOREIGN_KEY_CHECKS=0;'.PHP_EOL.PHP_EOL;
            $enableForeignKeysCheck = 'SET FOREIGN_KEY_CHECKS=1;'.PHP_EOL;;
        }

        if ($disableForeignKeysCheck !== null) {
            $rows[] = $disableForeignKeysCheck;
        }

        //Schema
        $rows += $this->dumpSchema($connection);

        //Data
        $userIds = [];
        if ($this->contextHandler->getContext() !== 'admin') {
            $userIds[] = "'" . $this->security->getUser()->getId() . "'";
        } else {
            foreach ($this->userRepository->findAll() as $user) {
                $userIds[] = "'" . $user->getId() . "'";
            };
        }
        $userIds = implode(',', $userIds);

        $selects = [
            "SELECT * FROM doctrine_migration_version",
            "SELECT * FROM koi_album WHERE owner_id IN ($userIds)",
            "SELECT * FROM koi_collection WHERE owner_id IN ($userIds)",
            "SELECT * FROM koi_datum WHERE owner_id IN ($userIds)",
            "SELECT f.* FROM koi_field f LEFT JOIN koi_template t ON f.template_id = t.id WHERE t.owner_id IN ($userIds)",
            "SELECT * FROM koi_inventory WHERE owner_id IN ($userIds)",
            "SELECT * FROM koi_item WHERE owner_id IN ($userIds)",
            "SELECT it.* FROM koi_item_tag it LEFT JOIN koi_item i ON it.item_id = i.id WHERE i.owner_id IN ($userIds)",
            "SELECT * FROM koi_loan WHERE owner_id IN ($userIds)",
            "SELECT * FROM koi_log WHERE owner_id IN ($userIds)",
            "SELECT * FROM koi_photo WHERE owner_id IN ($userIds)",
            "SELECT * FROM koi_tag WHERE owner_id IN ($userIds)",
            "SELECT * FROM koi_tag_category WHERE owner_id IN ($userIds)",
            "SELECT * FROM koi_template WHERE owner_id IN ($userIds)",
            "SELECT * FROM koi_user WHERE id IN ($userIds)",
            "SELECT * FROM koi_wish WHERE owner_id IN ($userIds)",
            "SELECT * FROM koi_wishlist WHERE owner_id IN ($userIds)",
        ];

        foreach ($selects as $select) {
            $stmt = $connection->prepare($select);
            $results =  $stmt->executeQuery()->fetchAllAssociative();

            if (empty($results)) {
                continue;
            }

            $explodedSelect = explode(' ', $select);
            $tableName = $explodedSelect[3];
            $entityName = ucfirst(substr($tableName, 4));

            $metadata = null;
            if (class_exists("App\Entity\\$entityName")) {
                $metadata = $this->managerRegistry->getManager()->getClassMetadata("App\Entity\\$entityName");
            }

            $headers = implode(',', \array_keys($results[0]));
            $rows[] = "INSERT INTO $tableName ($headers) VALUES ".PHP_EOL;

            $count = \count($results);
            foreach ($results as $key => $result) {
                $values = [];
                foreach ($result as $property => $value) {
                    $values[] = $this->formatValue($value, $property, $metadata);
                }

                $content = '('.implode(',', $values) .')';
                $content .= $key === $count - 1 ? PHP_EOL : ','.PHP_EOL;
                $rows[] = $content;
            }

            $rows[] = ';'.PHP_EOL;
            $rows[] = PHP_EOL;
        }

        //Enable foreign keys
        if ($enableForeignKeysCheck !== null) {
            $rows[] = $enableForeignKeysCheck;
        }

        return $rows;
    }

    public function dumpSchema(Connection $connection) : array
    {
        $currentSchema = $connection->getSchemaManager()->createSchema();
        $schemaRows = (new Schema())->getMigrateToSql($currentSchema, $connection->getDatabasePlatform());
        $rows = \array_map(function ($row) {
            return $row.';'.PHP_EOL;
        }, $schemaRows);
        $rows[] = PHP_EOL;

        return $rows;
    }

    private function formatValue($value, string $property, $metadata)
    {
        if (\is_string($value)) {
            $value = str_replace(['\\', "'"], ['\\\\', "''"], $value);
        }

        if ($value === null) {
            $value = 'NULL';
        } else {
            if ($metadata && $metadata->getTypeOfField(\array_search($property, $metadata->columnNames)) === 'boolean') {
                $value = $value === true ? 'true' : 'false';
            }

            if ($metadata === null || \in_array($metadata->getTypeOfField(\array_search($property, $metadata->columnNames)), [null, 'string', 'datetime', 'date' ,'uuid', 'array', 'text'], true)) {
                $value = "'" . $value . "'";
            }
        }

        return $value;
    }
}
