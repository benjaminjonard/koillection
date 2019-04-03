<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Enum\DateFormatEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190307155057 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add `date_format` property to `koi_user`.';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("ALTER TABLE koi_user ADD date_format VARCHAR(255) NOT NULL DEFAULT '" . DateFormatEnum::FORMAT_HYPHEN_YMD . "'");
        $this->addSql('ALTER TABLE koi_user ALTER timezone SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
