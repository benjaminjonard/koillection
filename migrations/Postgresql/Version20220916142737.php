<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\SortingDirectionEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220916142737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add list customisation properties `koi_collection`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD children_list_show_visibility BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD children_list_show_actions BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD children_list_show_number_of_children BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD children_list_show_number_of_items BOOLEAN DEFAULT true NOT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD children_sorting_type VARCHAR(10) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD children_sorting_property VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE koi_collection ADD children_sorting_direction VARCHAR(255)');
        $this->addSql('UPDATE koi_collection SET children_sorting_direction = ?', [SortingDirectionEnum::ASCENDING]);
        $this->addSql('ALTER TABLE koi_collection ALTER COLUMN children_sorting_direction SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
