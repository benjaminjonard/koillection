<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200727114843 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add relationship between `koi_datum` and `koi_collection`.';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_datum ADD collection_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE5514956FD FOREIGN KEY (collection_id) REFERENCES koi_collection (id)');
        $this->addSql('CREATE INDEX IDX_F991BE5514956FD ON koi_datum (collection_id)');

        $this->addSql('ALTER TABLE koi_item DROP FOREIGN KEY FK_3EBAA3025DA0FB8');
        $this->addSql('DROP INDEX IDX_3EBAA3025DA0FB8 ON koi_item');
        $this->addSql('ALTER TABLE koi_item DROP template_id');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
