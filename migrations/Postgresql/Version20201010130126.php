<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201010130126 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Remove themes and add better dark mode support';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_user ADD dark_mode_enabled BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE koi_user DROP theme');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
