<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190702133405 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add `idx_visibility` index to `koi_collection`.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE INDEX idx_visibility ON koi_collection (visibility)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
