<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\VisibilityEnum;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240329223202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add visibilities properties to `koi_datum`.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $publicVisibility = VisibilityEnum::VISIBILITY_PUBLIC;

        $this->addSql('ALTER TABLE koi_datum ADD visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_datum ADD parent_visibility VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_datum ADD final_visibility VARCHAR(10)');

        $this->addSql("
            UPDATE koi_datum 
            SET visibility = '$publicVisibility', parent_visibility = item.final_visibility, final_visibility = item.final_visibility
            FROM koi_item item
            WHERE item_id = item.id
        ");

        $this->addSql("
            UPDATE koi_datum 
            SET visibility = '$publicVisibility', parent_visibility = collection.final_visibility, final_visibility = collection.final_visibility
            FROM koi_collection collection
            WHERE collection_id = collection.id
        ");

        $this->addSql('ALTER TABLE koi_datum ALTER visibility SET NOT NULL');
        $this->addSql('ALTER TABLE koi_datum ALTER final_visibility SET NOT NULL');

        $this->addSql('CREATE INDEX idx_datum_final_visibility ON koi_datum (final_visibility)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
