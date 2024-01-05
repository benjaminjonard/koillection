<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\DisplayModeEnum;
use App\Enum\SortingDirectionEnum;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20220916154614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `koi_display_configuration`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE koi_display_configuration (id CHAR(36) NOT NULL, owner_id CHAR(36) DEFAULT NULL, label VARCHAR(255) DEFAULT NULL, display_mode VARCHAR(4) NOT NULL, sorting_property VARCHAR(255) DEFAULT NULL, sorting_type VARCHAR(10) DEFAULT NULL, sorting_direction VARCHAR(255) NOT NULL, show_visibility BOOLEAN DEFAULT true NOT NULL, show_actions BOOLEAN DEFAULT true NOT NULL, show_number_of_children BOOLEAN DEFAULT true NOT NULL, show_number_of_items BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8D988CCC7E3C61F9 ON koi_display_configuration (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_display_configuration.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_display_configuration.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_display_configuration ADD CONSTRAINT FK_8D988CCC7E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_album ADD children_display_configuration_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_album ADD CONSTRAINT FK_2DB8938A45644244 FOREIGN KEY (children_display_configuration_id) REFERENCES koi_display_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DB8938A45644244 ON koi_album (children_display_configuration_id)');
        $this->addSql('ALTER TABLE koi_collection ADD children_display_configuration_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B05745644244 FOREIGN KEY (children_display_configuration_id) REFERENCES koi_display_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7AA7B05745644244 ON koi_collection (children_display_configuration_id)');
        $this->addSql('ALTER TABLE koi_wishlist ADD children_display_configuration_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_wishlist ADD CONSTRAINT FK_98E338D245644244 FOREIGN KEY (children_display_configuration_id) REFERENCES koi_display_configuration (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_98E338D245644244 ON koi_wishlist (children_display_configuration_id)');

        $displayMode = DisplayModeEnum::DISPLAY_MODE_GRID;
        $sortingDirection = SortingDirectionEnum::ASCENDING;

        $collections = $this->connection->createQueryBuilder()->select('id, owner_id, children_title')->from('koi_collection')->executeQuery()->fetchAllAssociative();
        foreach ($collections as $collection) {
            $id = Uuid::v4()->toRfc4122();
            $collectionId = $collection['id'];
            $ownerId = $collection['owner_id'];
            $label = !empty($collection['children_title']) ? "'".$collection['children_title']."'" : 'NULL';

            $this->addSql("INSERT INTO koi_display_configuration (id, owner_id, label, display_mode, sorting_direction, created_at) VALUES ('$id', '$ownerId', $label, '$displayMode', '$sortingDirection', NOW())");
            $this->addSql("UPDATE koi_collection SET children_display_configuration_id = '$id' WHERE id = '$collectionId'");
        }

        $this->addSql('ALTER TABLE koi_collection DROP children_title');

        $albums = $this->connection->createQueryBuilder()->select('id, owner_id')->from('koi_album')->executeQuery()->fetchAllAssociative();
        foreach ($albums as $album) {
            $id = Uuid::v4()->toRfc4122();
            $albumId = $album['id'];
            $ownerId = $album['owner_id'];

            $this->addSql("INSERT INTO koi_display_configuration (id, owner_id, display_mode, sorting_direction, created_at) VALUES ('$id', '$ownerId', '$displayMode', '$sortingDirection', NOW())");
            $this->addSql("UPDATE koi_album SET children_display_configuration_id = '$id' WHERE id = '$albumId'");
        }

        $wishlists = $this->connection->createQueryBuilder()->select('id, owner_id')->from('koi_wishlist')->executeQuery()->fetchAllAssociative();
        foreach ($wishlists as $wishlist) {
            $id = Uuid::v4()->toRfc4122();
            $wishlistId = $wishlist['id'];
            $ownerId = $wishlist['owner_id'];

            $this->addSql("INSERT INTO koi_display_configuration (id, owner_id, display_mode, sorting_direction, created_at) VALUES ('$id', '$ownerId', '$displayMode', '$sortingDirection', NOW())");
            $this->addSql("UPDATE koi_wishlist SET children_display_configuration_id = '$id' WHERE id = '$wishlistId'");
        }
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
