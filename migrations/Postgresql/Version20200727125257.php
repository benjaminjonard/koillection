<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200727125257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add relationship between `koi_datum` and `koi_collection`.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_item DROP CONSTRAINT fk_3ebaa3025da0fb8');
        $this->addSql('DROP INDEX idx_3ebaa3025da0fb8');
        $this->addSql('ALTER TABLE koi_item DROP template_id');
        $this->addSql('ALTER TABLE koi_datum ADD collection_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN koi_datum.collection_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE5514956FD FOREIGN KEY (collection_id) REFERENCES koi_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F991BE5514956FD ON koi_datum (collection_id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
