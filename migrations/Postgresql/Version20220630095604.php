<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\DisplayModeEnum;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220630095604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `items_display_mode` property to `koi_collection`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD items_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_collection SET items_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_collection ALTER COLUMN items_display_mode SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
