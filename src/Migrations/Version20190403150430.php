<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190403150430 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add `object_deleted` property to `koi_log` and set it to true if there is a delete entry for the `object_id`.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_log ADD object_deleted BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('UPDATE koi_log SET object_deleted = true WHERE object_id IN (SELECT DISTINCT l.object_id FROM koi_log l WHERE l.type =  \'delete\')');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
