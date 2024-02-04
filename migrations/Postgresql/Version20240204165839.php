<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240204165839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Update deprecated Doctrine array type to json type.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->convertValueFromArrayToJson('koi_choice_list', 'choices');
        $this->addSql('ALTER TABLE koi_choice_list ALTER choices TYPE JSON USING choices::json');
        $this->addSql('COMMENT ON COLUMN koi_choice_list.choices IS NULL');

        $this->convertValueFromArrayToJson('koi_display_configuration', 'columns');
        $this->addSql('ALTER TABLE koi_display_configuration ALTER columns TYPE JSON USING columns::json');
        $this->addSql('COMMENT ON COLUMN koi_display_configuration.columns IS NULL');

        $this->convertValueFromArrayToJson('koi_user', 'roles');
        $this->addSql('ALTER TABLE koi_user ALTER roles TYPE JSON USING roles::json');
        $this->addSql('COMMENT ON COLUMN koi_user.roles IS NULL');
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
