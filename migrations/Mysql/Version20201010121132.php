<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201010121132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Remove themes and add better dark mode support';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('ALTER TABLE koi_user ADD dark_mode_enabled TINYINT(1) DEFAULT \'0\' NOT NULL, ADD automatic_dark_mode_start_at TIME DEFAULT NULL, ADD automatic_dark_mode_end_at TIME DEFAULT NULL, DROP theme');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
