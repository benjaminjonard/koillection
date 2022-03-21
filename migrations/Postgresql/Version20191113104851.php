<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20191113104851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Change `disk_space_allowed` and `disk_space_used` types from `int` to `bigint` for `koi_user` table.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_user ALTER disk_space_used TYPE BIGINT');
        $this->addSql('ALTER TABLE koi_user ALTER disk_space_used SET DEFAULT 0');
        $this->addSql('ALTER TABLE koi_user ALTER disk_space_allowed TYPE BIGINT');
        $this->addSql('ALTER TABLE koi_user ALTER disk_space_allowed SET DEFAULT 268435456');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
