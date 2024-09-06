<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\DisplayModeEnum;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240906163226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `search_results_display_mode` to `koi_user`.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $gridDisplayMode = DisplayModeEnum::DISPLAY_MODE_GRID;

        $this->addSql('ALTER TABLE koi_user ADD search_results_display_mode VARCHAR(255)');
        $this->addSql("UPDATE koi_user SET search_results_display_mode = '{$gridDisplayMode}'");
        $this->addSql('ALTER TABLE koi_user CHANGE search_results_display_mode search_results_display_mode VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
