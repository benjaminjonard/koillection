<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\DisplayModeEnum;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220701175209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `items_display_mode` property to `koi_tag` and to `koi_album`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('ALTER TABLE koi_album ADD photos_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_album SET photos_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_album MODIFY photos_display_mode VARCHAR(4) NOT NULL');

        $this->addSql('ALTER TABLE koi_tag ADD items_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_tag SET items_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_tag MODIFY items_display_mode VARCHAR(4) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
