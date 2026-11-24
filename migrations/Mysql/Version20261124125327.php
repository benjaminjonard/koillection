<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261124125327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add normalized columns used for accent-insensitive search.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform, 'Mysql or Mariadb migration only. Skipped.');

        $this->addSql('ALTER TABLE koi_album ADD normalized_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD normalized_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_datum ADD normalized_value LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_item ADD normalized_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_tag ADD normalized_label VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_wishlist ADD normalized_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
