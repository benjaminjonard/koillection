<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220206133834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add final and parent visibilities';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX idx_album_visibility ON koi_album');
        $this->addSql('ALTER TABLE koi_album ADD parent_visibility VARCHAR(10), ADD final_visibility VARCHAR(10), CHANGE visibility visibility VARCHAR(10) NOT NULL');
        $this->addSql('CREATE INDEX idx_album_final_visibility ON koi_album (final_visibility)');
        $this->addSql('UPDATE koi_album SET parent_visibility=visibility, final_visibility=visibility WHERE parent_id IS NOT NULL');
        $this->addSql('UPDATE koi_album SET final_visibility=visibility WHERE parent_id IS NULL');
        $this->addSql('ALTER TABLE koi_album CHANGE final_visibility final_visibility VARCHAR(10) NOT NULL');

        $this->addSql('DROP INDEX idx_collection_visibility ON koi_collection');
        $this->addSql('ALTER TABLE koi_collection ADD parent_visibility VARCHAR(10), ADD final_visibility VARCHAR(10), CHANGE visibility visibility VARCHAR(10) NOT NULL');
        $this->addSql('CREATE INDEX idx_collection_final_visibility ON koi_collection (final_visibility)');
        $this->addSql('UPDATE koi_collection SET parent_visibility=visibility, final_visibility=visibility WHERE parent_id IS NOT NULL');
        $this->addSql('UPDATE koi_collection SET final_visibility=visibility WHERE parent_id IS NULL');
        $this->addSql('ALTER TABLE koi_collection CHANGE final_visibility final_visibility VARCHAR(10) NOT NULL');

        $this->addSql('DROP INDEX idx_datum_visibility ON koi_datum');
        $this->addSql('ALTER TABLE koi_datum DROP visibility, CHANGE type type VARCHAR(10) NOT NULL');

        $this->addSql('DROP INDEX idx_item_visibility ON koi_item');
        $this->addSql('ALTER TABLE koi_item ADD parent_visibility VARCHAR(10), ADD final_visibility VARCHAR(10), CHANGE visibility visibility VARCHAR(10) NOT NULL');
        $this->addSql('CREATE INDEX idx_item_final_visibility ON koi_item (final_visibility)');
        $this->addSql('UPDATE koi_item SET parent_visibility=visibility, final_visibility=visibility');
        $this->addSql('ALTER TABLE koi_item CHANGE final_visibility final_visibility VARCHAR(10) NOT NULL');

        $this->addSql('ALTER TABLE koi_log CHANGE type type VARCHAR(6) DEFAULT NULL, CHANGE object_id object_id VARCHAR(36) NOT NULL');

        $this->addSql('DROP INDEX idx_photo_visibility ON koi_photo');
        $this->addSql('ALTER TABLE koi_photo ADD parent_visibility VARCHAR(10), ADD final_visibility VARCHAR(10), CHANGE visibility visibility VARCHAR(10) NOT NULL');
        $this->addSql('CREATE INDEX idx_photo_final_visibility ON koi_photo (final_visibility)');
        $this->addSql('UPDATE koi_photo SET parent_visibility=visibility, final_visibility=visibility');
        $this->addSql('ALTER TABLE koi_photo CHANGE final_visibility final_visibility VARCHAR(10) NOT NULL');

        $this->addSql('ALTER TABLE koi_tag CHANGE visibility visibility VARCHAR(10) NOT NULL');

        $this->addSql('ALTER TABLE koi_user CHANGE timezone timezone VARCHAR(50) NOT NULL, CHANGE date_format date_format VARCHAR(10) NOT NULL, CHANGE visibility visibility VARCHAR(10) NOT NULL');

        $this->addSql('DROP INDEX idx_wish_visibility ON koi_wish');
        $this->addSql('ALTER TABLE koi_wish ADD parent_visibility VARCHAR(10), ADD final_visibility VARCHAR(10), CHANGE visibility visibility VARCHAR(10) NOT NULL');
        $this->addSql('CREATE INDEX idx_wish_final_visibility ON koi_wish (final_visibility)');
        $this->addSql('UPDATE koi_wish SET parent_visibility=visibility, final_visibility=visibility');
        $this->addSql('ALTER TABLE koi_wish CHANGE final_visibility final_visibility VARCHAR(10) NOT NULL');

        $this->addSql('DROP INDEX idx_wishlist_visibility ON koi_wishlist');
        $this->addSql('ALTER TABLE koi_wishlist ADD parent_visibility VARCHAR(10), ADD final_visibility VARCHAR(10), CHANGE visibility visibility VARCHAR(10) NOT NULL');
        $this->addSql('CREATE INDEX idx_wishlist_final_visibility ON koi_wishlist (final_visibility)');
        $this->addSql('UPDATE koi_wishlist SET parent_visibility=visibility, final_visibility=visibility WHERE parent_id IS NOT NULL');
        $this->addSql('UPDATE koi_wishlist SET final_visibility=visibility WHERE parent_id IS NULL');
        $this->addSql('ALTER TABLE koi_wishlist CHANGE final_visibility final_visibility VARCHAR(10) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
