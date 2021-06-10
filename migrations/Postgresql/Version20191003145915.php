<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191003145915 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '[Postgresql] Add `tag_category` table.';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE koi_tag_category (id UUID NOT NULL, owner_id UUID DEFAULT NULL, label VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, color VARCHAR(7) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DE4E5D497E3C61F9 ON koi_tag_category (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_tag_category.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_tag_category.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_tag_category ADD CONSTRAINT FK_DE4E5D497E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_tag ADD category_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN koi_tag.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_tag ADD CONSTRAINT FK_16FB1EB712469DE2 FOREIGN KEY (category_id) REFERENCES koi_tag_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_16FB1EB712469DE2 ON koi_tag (category_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
