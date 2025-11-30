<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251130000942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `koi_import` table.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Mysql or Mariadb migration only. Skipped.');

        $this->addSql('CREATE TABLE koi_import (id CHAR(36) NOT NULL, file VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, name_index INT DEFAULT NULL, image_index INT DEFAULT NULL, mapping JSON DEFAULT NULL, total_number_of_items INT DEFAULT NULL, number_of_imported_items INT DEFAULT NULL, number_of_duplicated_items INT DEFAULT NULL, number_of_skipped_items INT DEFAULT NULL, created_at DATETIME NOT NULL, collection_id CHAR(36) DEFAULT NULL, owner_id CHAR(36) DEFAULT NULL, UNIQUE INDEX UNIQ_7FE294F48C9F3610 (file), INDEX IDX_7FE294F4514956FD (collection_id), INDEX IDX_7FE294F47E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE koi_import ADD CONSTRAINT FK_7FE294F4514956FD FOREIGN KEY (collection_id) REFERENCES koi_collection (id)');
        $this->addSql('ALTER TABLE koi_import ADD CONSTRAINT FK_7FE294F47E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
