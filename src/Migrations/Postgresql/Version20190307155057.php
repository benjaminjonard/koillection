<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

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
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql("ALTER TABLE koi_user ADD date_format VARCHAR(255) NOT NULL DEFAULT 'Y-m-d'");
        $this->addSql('ALTER TABLE koi_user ALTER timezone SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
