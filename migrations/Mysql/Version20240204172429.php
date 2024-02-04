<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240204172429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Update deprecated Doctrine array type to json type.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->convertValueFromArrayToJson('koi_choice_list', 'choices');
        $this->addSql('ALTER TABLE koi_choice_list CHANGE choices choices JSON NOT NULL');

        $this->convertValueFromArrayToJson('koi_display_configuration', 'columns');
        $this->addSql('ALTER TABLE koi_display_configuration CHANGE columns columns JSON DEFAULT NULL');

        $this->convertValueFromArrayToJson('koi_user', 'roles');
        $this->addSql('ALTER TABLE koi_user CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }

    private function convertValueFromArrayToJson($table, $property): void
    {
        $rows = $this->connection->createQueryBuilder()->select("id, $property")->from($table)->executeQuery()->fetchAllAssociative();
        foreach ($rows as $row) {
            $id = $row['id'];
            $data = $row[$property];
            $encodedData = unserialize($data);
            $encodedData = json_encode($encodedData);

            $this->addSql("UPDATE $table SET $property = '$encodedData' WHERE id = '$id'");
        }
    }
}
