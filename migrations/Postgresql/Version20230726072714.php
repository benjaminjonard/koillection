<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230726072714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Rename list to choice-list';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_datum ALTER type TYPE VARCHAR(15)');
        $this->addSql('ALTER TABLE koi_display_configuration ALTER sorting_type TYPE VARCHAR(15)');
        $this->addSql('ALTER TABLE koi_field ALTER type TYPE VARCHAR(15)');

        $this->addSql("UPDATE koi_datum SET type = 'choice-list' WHERE type = 'list';");
        $this->addSql("UPDATE koi_display_configuration SET sorting_type = 'choice-list' WHERE sorting_type = 'list';");
        $this->addSql("UPDATE koi_field SET type = 'choice-list' WHERE type = 'list';");
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
