<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201215214207 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add `large_thumbnail` property on `koi_item` and `koi_datum`';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_item ADD image_large_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EBAA302A69A0691 ON koi_item (image_large_thumbnail)');
        $this->addSql('ALTER TABLE koi_datum ADD image_large_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F991BE5A69A0691 ON koi_datum (image_large_thumbnail)');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
