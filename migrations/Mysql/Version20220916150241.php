<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\DisplayModeEnum;
use App\Enum\SortingDirectionEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220916150241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add list customisation properties `koi_collection`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_album ADD children_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_album SET children_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_album MODIFY children_display_mode VARCHAR(4) NOT NULL');


        $this->addSql('ALTER TABLE koi_collection ADD children_list_show_visibility TINYINT(1) DEFAULT 1 NOT NULL, ADD children_list_show_actions TINYINT(1) DEFAULT 1 NOT NULL, ADD children_list_show_number_of_children TINYINT(1) DEFAULT 1 NOT NULL, ADD children_list_show_number_of_items TINYINT(1) DEFAULT 1 NOT NULL, ADD children_sorting_property VARCHAR(255) DEFAULT NULL, ADD children_sorting_type VARCHAR(10) DEFAULT NULL');

        $this->addSql('ALTER TABLE koi_collection ADD children_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_collection SET children_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_collection MODIFY children_display_mode VARCHAR(4) NOT NULL');

        $this->addSql('ALTER TABLE koi_collection ADD children_sorting_direction VARCHAR(255)');
        $this->addSql('UPDATE koi_collection SET children_sorting_direction = ?', [SortingDirectionEnum::ASCENDING]);
        $this->addSql('ALTER TABLE koi_collection MODIFY children_sorting_direction VARCHAR(4) NOT NULL');

        $this->addSql('ALTER TABLE koi_wishlist ADD children_display_mode VARCHAR(4)');
        $this->addSql('UPDATE koi_wishlist SET children_display_mode = ?', [DisplayModeEnum::DISPLAY_MODE_GRID]);
        $this->addSql('ALTER TABLE koi_wishlist MODIFY children_display_mode VARCHAR(4) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
