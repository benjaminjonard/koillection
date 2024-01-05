<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\SortingDirectionEnum;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220713065947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add sorting properties for items in collections';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD items_sorting_property VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE koi_collection ADD items_sorting_direction VARCHAR(255)');
        $this->addSql('UPDATE koi_collection SET items_sorting_direction = ?', [SortingDirectionEnum::ASCENDING]);
        $this->addSql('ALTER TABLE koi_collection ALTER COLUMN items_sorting_direction SET NOT NULL');

        $this->addSql('CREATE INDEX idx_datum_label ON koi_datum (label)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
