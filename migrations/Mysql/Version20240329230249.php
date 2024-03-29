<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\VisibilityEnum;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240329230249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add visibilities properties to `koi_datum`.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $publicVisibility = VisibilityEnum::VISIBILITY_PUBLIC;

        $this->addSql('ALTER TABLE koi_datum ADD visibility VARCHAR(10), ADD parent_visibility VARCHAR(10) DEFAULT NULL, ADD final_visibility VARCHAR(10)');

        $this->addSql("
            UPDATE koi_datum datum
            JOIN koi_item item ON datum.item_id = item.id
            SET datum.visibility = '$publicVisibility', datum.parent_visibility = item.final_visibility, datum.final_visibility = item.final_visibility        
        ");

        $this->addSql("
            UPDATE koi_datum datum
            JOIN koi_collection collection ON datum.collection_id = collection.id
            SET datum.visibility = '$publicVisibility', datum.parent_visibility = collection.final_visibility, datum.final_visibility = collection.final_visibility
        ");

        $this->addSql('ALTER TABLE koi_datum CHANGE visibility visibility VARCHAR(10) NOT NULL');
        $this->addSql('ALTER TABLE koi_datum CHANGE final_visibility final_visibility VARCHAR(10) NOT NULL');

        $this->addSql('CREATE INDEX idx_datum_final_visibility ON koi_datum (final_visibility)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
