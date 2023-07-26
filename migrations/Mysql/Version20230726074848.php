<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230726074848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Rename list to choice-list';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_datum CHANGE type type VARCHAR(15) NOT NULL');
        $this->addSql('ALTER TABLE koi_display_configuration CHANGE sorting_type sorting_type VARCHAR(15) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_field CHANGE type type VARCHAR(15) NOT NULL');

        $this->addSql("UPDATE koi_datum SET type = 'choice-list' WHERE type = 'list';");
        $this->addSql("UPDATE koi_display_configuration SET sorting_type = 'choice-list' WHERE sorting_type = 'list';");
        $this->addSql("UPDATE koi_field SET type = 'choice-list' WHERE type = 'list';");
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
