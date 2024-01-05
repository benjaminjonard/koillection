<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230801084644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `koi_scrapper` and `koi_path`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE koi_path (id CHAR(36) NOT NULL, scraper_id CHAR(36) DEFAULT NULL, owner_id CHAR(36) DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(15) NOT NULL, path TEXT NOT NULL, position INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2AF50D135A68BBF9 ON koi_path (scraper_id)');
        $this->addSql('CREATE INDEX IDX_2AF50D137E3C61F9 ON koi_path (owner_id)');
        $this->addSql('CREATE TABLE koi_scraper (id CHAR(36) NOT NULL, owner_id CHAR(36) DEFAULT NULL, name VARCHAR(255) NOT NULL, name_path TEXT DEFAULT NULL, image_path TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E5405AD17E3C61F9 ON koi_scraper (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_scraper.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_scraper.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_path ADD CONSTRAINT FK_2AF50D135A68BBF9 FOREIGN KEY (scraper_id) REFERENCES koi_scraper (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_path ADD CONSTRAINT FK_2AF50D137E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_scraper ADD CONSTRAINT FK_E5405AD17E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_user ADD scraping_feature_enabled BOOLEAN DEFAULT true NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
