<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250120214033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add search tables.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform, 'Mysql or Mariadb migration only. Skipped.');

        $this->addSql('CREATE TABLE koi_search (id CHAR(36) NOT NULL, name VARCHAR(255) DEFAULT NULL, owner_id CHAR(36) DEFAULT NULL, INDEX IDX_565C814E7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE koi_search_block (id CHAR(36) NOT NULL, `condition` VARCHAR(255) DEFAULT NULL, search_id CHAR(36) DEFAULT NULL, INDEX IDX_591D6B98650760A9 (search_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE koi_search_filter (id CHAR(36) NOT NULL, `condition` VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, datum_label VARCHAR(255) DEFAULT NULL, datum_type VARCHAR(255) DEFAULT NULL, operator VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, block_id CHAR(36) DEFAULT NULL, INDEX IDX_54AA0373E9ED820C (block_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE koi_search ADD CONSTRAINT FK_565C814E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id)');
        $this->addSql('ALTER TABLE koi_search_block ADD CONSTRAINT FK_591D6B98650760A9 FOREIGN KEY (search_id) REFERENCES koi_search (id)');
        $this->addSql('ALTER TABLE koi_search_filter ADD CONSTRAINT FK_54AA0373E9ED820C FOREIGN KEY (block_id) REFERENCES koi_search_block (id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
