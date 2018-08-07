<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class DatabaseDumper
 *
 * @package App\Service
 */
class DatabaseDumper
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * DatabaseDumper constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function dump() : array
    {
        $connection = $this->em->getConnection();

        //Disable foreign keys
        $rows[] = 'SET session_replication_role = replica;'.PHP_EOL.PHP_EOL;

        //Schema
        $rows += $this->dumpSchema($connection);

        //Data
        $userId = $this->tokenStorage->getToken()->getUser()->getId();
        $selects = [
            "SELECT * FROM doctrine_migration_version",
            "SELECT * FROM koi_user WHERE id = '$userId'",
            "SELECT * FROM koi_medium WHERE owner_id = '$userId'",
            "SELECT * FROM koi_log WHERE user_id = '$userId'",
            "SELECT * FROM koi_collection WHERE owner_id = '$userId'",
            "SELECT * FROM koi_item WHERE owner_id = '$userId'",
            "SELECT * FROM koi_datum WHERE owner_id = '$userId'",
            "SELECT * FROM koi_loan WHERE owner_id = '$userId'",
            "SELECT * FROM koi_tag WHERE owner_id = '$userId'",
            "SELECT it.* FROM koi_item_tag it LEFT JOIN koi_item i ON it.item_id = i.id WHERE i.owner_id = '$userId'",
            "SELECT * FROM koi_template WHERE owner_id = '$userId'",
            "SELECT f.* FROM koi_field f LEFT JOIN koi_template t ON f.template_id = t.id WHERE t.owner_id = '$userId'",
            "SELECT * FROM koi_wishlist WHERE owner_id = '$userId'",
            "SELECT * FROM koi_wish WHERE owner_id = '$userId'",
            "SELECT * FROM koi_album WHERE owner_id = '$userId'",
            "SELECT * FROM koi_photo WHERE owner_id = '$userId'",
        ];

        foreach ($selects as $select) {
            $stmt = $connection->prepare($select);
            $stmt->execute();
            $results = $stmt->fetchAll();

            if (empty($results)) {
                continue;
            }

            $explodedSelect = explode(' ', $select);
            $tableName = $explodedSelect[3];
            $entityName = ucfirst(substr($tableName, 4));

            $metadata = null;
            if (class_exists("App\Entity\\$entityName")) {
                $metadata = $this->em->getClassMetadata("App\Entity\\$entityName");
            }

            $headers = implode(',', array_keys($results[0]));
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
        $rows[] = 'SET session_replication_role = DEFAULT;'.PHP_EOL;

        return $rows;
    }

    /**
     * @param Connection $connection
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function dumpSchema(Connection $connection) : array
    {
        $currentSchema = $connection->getSchemaManager()->createSchema();
        $schemaRows = (new Schema())->getMigrateToSql($currentSchema, $connection->getDatabasePlatform());
        $rows = array_map(function ($row) { return $row.';'.PHP_EOL; }, $schemaRows);
        $rows[] = PHP_EOL;

        return $rows;
    }

    /**
     * @param $value
     * @param string $property
     * @param $metadata
     * @return mixed|string
     */
    private function formatValue($value, string $property, $metadata)
    {
        if (\is_string($value)) {
            $value = str_replace(['\\', "'"], ['\\\\', "''"] , $value);
        }

        if ($value === null) {
            $value = 'NULL';
        } else {
            if ($metadata && $metadata->getTypeOfField(array_search($property, $metadata->columnNames)) === 'boolean') {
                $value = $value === true ? 'true' : 'false';
            }

            if ($metadata === null || \in_array($metadata->getTypeOfField(array_search($property, $metadata->columnNames)), [null, 'string', 'datetime', 'date' ,'uuid', 'array', 'text'], true)) {
                $value = "'" . $value . "'";
            }
        }

        return $value;
    }
}
