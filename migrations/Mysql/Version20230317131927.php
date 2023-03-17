<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230317131927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `theme` to `koi_user`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_user ADD theme VARCHAR(255) DEFAULT \'browser\' NOT NULL, DROP dark_mode_enabled, DROP automatic_dark_mode_start_at, DROP automatic_dark_mode_end_at');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
