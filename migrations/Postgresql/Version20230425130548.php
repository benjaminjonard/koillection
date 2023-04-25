<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230425130548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `search_in_data_by_default_enabled` to `koi_user`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_user ADD search_in_data_by_default_enabled BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE koi_user ALTER disk_space_allowed SET DEFAULT 536870912');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
