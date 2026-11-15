<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261115200744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `description` column on `koi_item`.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform, 'Mysql or Mariadb migration only. Skipped.');

        $this->addSql('ALTER TABLE koi_item ADD description LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
