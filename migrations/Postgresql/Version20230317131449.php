<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230317131449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `theme` to `koi_user`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_user ADD theme VARCHAR(255) DEFAULT \'browser\' NOT NULL');
        $this->addSql('ALTER TABLE koi_user DROP dark_mode_enabled');
        $this->addSql('ALTER TABLE koi_user DROP automatic_dark_mode_start_at');
        $this->addSql('ALTER TABLE koi_user DROP automatic_dark_mode_end_at');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
