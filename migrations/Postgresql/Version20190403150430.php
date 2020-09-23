<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190403150430 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add `object_deleted` property to `koi_log`.';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_log ADD object_deleted BOOLEAN DEFAULT \'false\' NOT NULL');

        //Set `object_deleted` to true if there is a delete entry for the `object_id`.
        $this->addSql('
            UPDATE koi_log 
            SET object_deleted = true 
            WHERE object_id IN (
                SELECT DISTINCT l.object_id 
                FROM koi_log l 
                WHERE l.type =  \'delete\'
            )
        ');

        //Delete some logs that shouldn't exist
        $this->addSql('
            DELETE FROM koi_log WHERE id IN (
                SELECT updated.id
                FROM koi_log AS updated
                INNER JOIN koi_log AS deleted
                    ON updated.object_id = deleted.object_id
                    AND updated.logged_at = deleted.logged_at
                    AND deleted.type = \'delete\'
                WHERE updated.type = \'update\'
            )    
        ');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
