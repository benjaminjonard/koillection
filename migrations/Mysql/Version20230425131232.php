<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230425131232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `search_in_data_by_default_enabled` to `koi_user`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_user ADD search_in_data_by_default_enabled TINYINT(1) DEFAULT 0 NOT NULL, CHANGE disk_space_allowed disk_space_allowed BIGINT DEFAULT 536870912 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
