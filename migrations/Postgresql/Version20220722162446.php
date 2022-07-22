<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220722162446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `items_display_mode_list_columns` to `koi_collection` and `koi_choice_list` table';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE koi_choice_list (id CHAR(36) NOT NULL, owner_id CHAR(36) DEFAULT NULL, name VARCHAR(255) NOT NULL, choices TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2C0938397E3C61F9 ON koi_choice_list (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_choice_list.choices IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE koi_choice_list ADD CONSTRAINT FK_2C0938397E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_collection ADD items_display_mode_list_columns TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN koi_collection.items_display_mode_list_columns IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE koi_datum ADD choice_list_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE5448D2C5 FOREIGN KEY (choice_list_id) REFERENCES koi_choice_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F991BE5448D2C5 ON koi_datum (choice_list_id)');
        $this->addSql('ALTER TABLE koi_field ADD choice_list_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_field ADD CONSTRAINT FK_4FD5B891448D2C5 FOREIGN KEY (choice_list_id) REFERENCES koi_choice_list (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4FD5B891448D2C5 ON koi_field (choice_list_id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');

    }
}
