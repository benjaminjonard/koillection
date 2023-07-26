<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230726163240 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add table `koi_scraper`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE koi_scraper (id CHAR(36) NOT NULL, owner_id CHAR(36) DEFAULT NULL, name VARCHAR(255) NOT NULL, name_path TEXT DEFAULT NULL, image_path TEXT DEFAULT NULL, data_paths JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8256B8887E3C61F9 ON koi_scraper (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_scraper.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_scraper.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_scraper ADD CONSTRAINT FK_8256B8887E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
