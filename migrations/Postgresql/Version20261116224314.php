<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261116224314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `first_day_of_week` to `koi_user`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Postgresql migration only. Skipped.');

        $this->addSql('ALTER TABLE koi_user ADD first_day_of_week SMALLINT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
