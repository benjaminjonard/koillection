<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230726163915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add table `koi_scraper`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE koi_scraper (id CHAR(36) NOT NULL, owner_id CHAR(36) DEFAULT NULL, name VARCHAR(255) NOT NULL, name_path LONGTEXT DEFAULT NULL, image_path LONGTEXT DEFAULT NULL, data_paths JSON DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8256B8887E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE koi_scraper ADD CONSTRAINT FK_8256B8887E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
