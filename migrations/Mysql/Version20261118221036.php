<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261118221036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `koi_import` and `doctrine_messenger_message` tables.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform, 'Mysql or Mariadb migration only. Skipped.');

        $this->addSql('CREATE TABLE koi_import (id CHAR(36) NOT NULL, filename VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, total_rows INT NOT NULL, processed_rows INT NOT NULL, imported_items INT NOT NULL, errors JSON NOT NULL, created_at DATETIME NOT NULL, finished_at DATETIME DEFAULT NULL, collection_id CHAR(36) DEFAULT NULL, owner_id CHAR(36) DEFAULT NULL, INDEX IDX_7FE294F4514956FD (collection_id), INDEX IDX_7FE294F47E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE doctrine_messenger_message (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_FA96D316FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE koi_import ADD CONSTRAINT FK_7FE294F4514956FD FOREIGN KEY (collection_id) REFERENCES koi_collection (id)');
        $this->addSql('ALTER TABLE koi_import ADD CONSTRAINT FK_7FE294F47E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
