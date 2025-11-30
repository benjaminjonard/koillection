<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250820093147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `koi_import` table.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Postgresql migration only. Skipped.');

        $this->addSql('CREATE TABLE koi_import (id CHAR(36) NOT NULL, file VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, collection_id CHAR(36) DEFAULT NULL, owner_id CHAR(36) DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7FE294F48C9F3610 ON koi_import (file)');
        $this->addSql('CREATE INDEX IDX_7FE294F4514956FD ON koi_import (collection_id)');
        $this->addSql('CREATE INDEX IDX_7FE294F47E3C61F9 ON koi_import (owner_id)');

        $this->addSql('ALTER TABLE koi_import ADD CONSTRAINT FK_7FE294F4514956FD FOREIGN KEY (collection_id) REFERENCES koi_collection (id)');
        $this->addSql('ALTER TABLE koi_import ADD CONSTRAINT FK_7FE294F47E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id)');
        $this->addSql('ALTER TABLE koi_import ADD mapping JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_import ADD total_number_of_items INT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_import ADD number_of_imported_items INT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_import ADD number_of_duplicated_items INT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_import ADD number_of_skipped_items INT DEFAULT NULL');

        $this->addSql('ALTER TABLE koi_import ADD name_index INT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_import ADD image_index INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
