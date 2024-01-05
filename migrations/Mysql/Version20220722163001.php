<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220722163001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `items_display_mode_list_columns` to `koi_collection` and `koi_choice_list` table';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('CREATE TABLE koi_choice_list (id CHAR(36) NOT NULL, owner_id CHAR(36) DEFAULT NULL, name VARCHAR(255) NOT NULL, choices LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_2C0938397E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE koi_choice_list ADD CONSTRAINT FK_2C0938397E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id)');
        $this->addSql('ALTER TABLE koi_collection ADD items_display_mode_list_columns LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE koi_datum ADD choice_list_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE5448D2C5 FOREIGN KEY (choice_list_id) REFERENCES koi_choice_list (id)');
        $this->addSql('CREATE INDEX IDX_F991BE5448D2C5 ON koi_datum (choice_list_id)');
        $this->addSql('ALTER TABLE koi_field ADD choice_list_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_field ADD CONSTRAINT FK_4FD5B891448D2C5 FOREIGN KEY (choice_list_id) REFERENCES koi_choice_list (id)');
        $this->addSql('CREATE INDEX IDX_4FD5B891448D2C5 ON koi_field (choice_list_id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
