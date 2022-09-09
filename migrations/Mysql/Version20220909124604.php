<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220909124604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `items_list_show_visibility` and `items_list_show_actions` properties to `koi_collection`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD items_list_show_visibility TINYINT(1) DEFAULT 1 NOT NULL, ADD items_list_show_actions TINYINT(1) DEFAULT 1 NOT NULL, CHANGE items_display_mode_list_columns items_list_columns LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
