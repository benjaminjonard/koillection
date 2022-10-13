<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\SortingDirectionEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20220916220210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add display_configuration for `koi_collection` and `koi_album`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_album ADD photos_display_configuration_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_album ADD CONSTRAINT FK_2DB8938A5DC99D4D FOREIGN KEY (photos_display_configuration_id) REFERENCES koi_display_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DB8938A5DC99D4D ON koi_album (photos_display_configuration_id)');

        $this->addSql('ALTER TABLE koi_collection ADD items_display_configuration_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B057A4A07C77 FOREIGN KEY (items_display_configuration_id) REFERENCES koi_display_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7AA7B057A4A07C77 ON koi_collection (items_display_configuration_id)');

        $this->addSql('ALTER TABLE koi_display_configuration ADD columns TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN koi_display_configuration.columns IS \'(DC2Type:simple_array)\'');

        $collections = $this->connection->createQueryBuilder()->select('id, owner_id, items_title, items_display_mode, items_sorting_property, items_sorting_direction, items_sorting_type, items_list_columns, items_list_show_visibility, items_list_show_actions')->from('koi_collection')->executeQuery()->fetchAllAssociative();
        foreach ($collections as $collection) {
            $id = Uuid::v4()->toRfc4122();
            $collectionId = $collection['id'];
            $ownerId = $collection['owner_id'];
            $label = !empty($collection['items_title']) ? "'".$collection['items_title']."'" : 'NULL';
            $displayMode = !empty($collection['items_display_mode']) ? "'".$collection['items_display_mode']."'" : 'NULL';
            $sortingProperty = !empty($collection['items_sorting_property']) ? "'".$collection['items_sorting_property']."'" : 'NULL';
            $sortingDirection = !empty($collection['items_sorting_direction']) ? "'".$collection['items_sorting_direction']."'" : "'".SortingDirectionEnum::ASCENDING."'";
            $sortingType = !empty($collection['items_sorting_type']) ? "'".$collection['items_sorting_type']."'" : 'NULL';
            $columns = !empty($collection['items_list_columns']) ? "'".$collection['items_list_columns']."'" : 'NULL';
            $showVisibility = $collection['items_list_show_visibility'] ? "'true'" : "'false'";
            $showActions = $collection['items_list_show_actions'] ? "'true'" : "'false'";

            $this->addSql("INSERT INTO koi_display_configuration (id, owner_id, label, display_mode, sorting_property, sorting_direction, sorting_type, columns, show_visibility, show_actions, created_at) VALUES ('$id', '$ownerId', $label, $displayMode, $sortingProperty, $sortingDirection, $sortingType, $columns, $showVisibility, $showActions, NOW())");
            $this->addSql("UPDATE koi_collection SET items_display_configuration_id = '$id' WHERE id = '$collectionId'");
        }

        $this->addSql('ALTER TABLE koi_collection DROP items_title');
        $this->addSql('ALTER TABLE koi_collection DROP items_display_mode');
        $this->addSql('ALTER TABLE koi_collection DROP items_sorting_property');
        $this->addSql('ALTER TABLE koi_collection DROP items_sorting_direction');
        $this->addSql('ALTER TABLE koi_collection DROP items_sorting_type');
        $this->addSql('ALTER TABLE koi_collection DROP items_list_columns');
        $this->addSql('ALTER TABLE koi_collection DROP items_list_show_visibility');
        $this->addSql('ALTER TABLE koi_collection DROP items_list_show_actions');

        $albums = $this->connection->createQueryBuilder()->select('id, owner_id, photos_display_mode')->from('koi_album')->executeQuery()->fetchAllAssociative();
        foreach ($albums as $album) {
            $id = Uuid::v4()->toRfc4122();
            $albumId = $album['id'];
            $ownerId = $album['owner_id'];
            $displayMode = !empty($album['photos_display_mode']) ? "'".$album['photos_display_mode']."'" : 'NULL';
            $sortingDirection = "'".SortingDirectionEnum::ASCENDING."'";

            $this->addSql("INSERT INTO koi_display_configuration (id, owner_id, display_mode, sorting_direction, created_at) VALUES ('$id', '$ownerId', $displayMode, $sortingDirection, NOW())");
            $this->addSql("UPDATE koi_album SET photos_display_configuration_id = '$id' WHERE id = '$albumId'");
        }

        $this->addSql('ALTER TABLE koi_album DROP photos_display_mode');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
