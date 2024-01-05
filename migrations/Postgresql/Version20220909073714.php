<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220909073714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `items_list_show_visibility` and `items_list_show_actions` properties to `koi_collection`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD items_list_show_visibility BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD items_list_show_actions BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE koi_collection RENAME COLUMN items_display_mode_list_columns TO items_list_columns');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
