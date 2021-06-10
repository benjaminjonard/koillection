<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210127105342 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '[Mysql] Add `koi_item_related_item` table';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE koi_item_related_item (item_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', related_item_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_A78A49D126F525E (item_id), INDEX IDX_A78A49D2D7698FB (related_item_id), PRIMARY KEY(item_id, related_item_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE koi_item_related_item ADD CONSTRAINT FK_A78A49D126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id)');
        $this->addSql('ALTER TABLE koi_item_related_item ADD CONSTRAINT FK_A78A49D2D7698FB FOREIGN KEY (related_item_id) REFERENCES koi_item (id)');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
