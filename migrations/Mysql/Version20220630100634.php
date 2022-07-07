<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\DisplayModeEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220630100634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `items_display_mode` property to `koi_collection`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD items_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_collection SET items_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_collection MODIFY items_display_mode VARCHAR(4) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
