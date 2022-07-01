<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\DisplayModeEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220701151517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `items_display_mode` property to `koi_tag` and to `koi_album`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_album ADD photos_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_album SET photos_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_album ALTER COLUMN photos_display_mode SET NOT NULL');

        $this->addSql('ALTER TABLE koi_tag ADD items_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_tag SET items_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_tag ALTER COLUMN items_display_mode SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
