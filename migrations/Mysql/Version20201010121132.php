<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201010121132 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Remove themes and add better dark mode support';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_user ADD dark_mode_enabled TINYINT(1) DEFAULT \'0\' NOT NULL, DROP theme');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
