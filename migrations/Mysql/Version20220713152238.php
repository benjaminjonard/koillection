<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\SortingDirectionEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220713152238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add sorting properties for items in collections';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD items_sorting_property VARCHAR(255) DEFAULT NULL, ADD items_sorting_direction VARCHAR(255)');
        $this->addSql('UPDATE koi_collection SET items_sorting_direction = ?', [SortingDirectionEnum::ASCENDING]);
        $this->addSql('ALTER TABLE koi_collection MODIFY items_sorting_direction VARCHAR(255) NOT NULL');

        $this->addSql('CREATE INDEX idx_datum_label ON koi_datum (label)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
