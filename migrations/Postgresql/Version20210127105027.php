<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210127105027 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '[Postgresql] Add `koi_item_related_item` table';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE koi_item_related_item (item_id UUID NOT NULL, related_item_id UUID NOT NULL, PRIMARY KEY(item_id, related_item_id))');
        $this->addSql('CREATE INDEX IDX_A78A49D126F525E ON koi_item_related_item (item_id)');
        $this->addSql('CREATE INDEX IDX_A78A49D2D7698FB ON koi_item_related_item (related_item_id)');
        $this->addSql('COMMENT ON COLUMN koi_item_related_item.item_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item_related_item.related_item_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_item_related_item ADD CONSTRAINT FK_A78A49D126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item_related_item ADD CONSTRAINT FK_A78A49D2D7698FB FOREIGN KEY (related_item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
