<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230801111844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `koi_scrapper` and `koi_path`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('CREATE TABLE koi_path (id CHAR(36) NOT NULL, scraper_id CHAR(36) DEFAULT NULL, owner_id CHAR(36) DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(15) NOT NULL, path LONGTEXT NOT NULL, position INT NOT NULL, INDEX IDX_2AF50D135A68BBF9 (scraper_id), INDEX IDX_2AF50D137E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE koi_scraper (id CHAR(36) NOT NULL, owner_id CHAR(36) DEFAULT NULL, name VARCHAR(255) NOT NULL, name_path LONGTEXT DEFAULT NULL, image_path LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E5405AD17E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE koi_path ADD CONSTRAINT FK_2AF50D135A68BBF9 FOREIGN KEY (scraper_id) REFERENCES koi_scraper (id)');
        $this->addSql('ALTER TABLE koi_path ADD CONSTRAINT FK_2AF50D137E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id)');
        $this->addSql('ALTER TABLE koi_scraper ADD CONSTRAINT FK_E5405AD17E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id)');
        $this->addSql('ALTER TABLE koi_user ADD scraping_feature_enabled TINYINT(1) DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
