<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190314154104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Create `koi_inventory` table.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE koi_inventory (id UUID NOT NULL, owner_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, content JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_882AFBE67E3C61F9 ON koi_inventory (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_inventory.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_inventory.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_inventory ADD CONSTRAINT FK_882AFBE67E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_user ALTER date_format DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
