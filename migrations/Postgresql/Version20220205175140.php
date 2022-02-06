<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220205175140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add final and parent visibilities';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX idx_album_visibility');
        $this->addSql('ALTER TABLE koi_album ADD parent_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_album ADD final_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_album ALTER visibility TYPE VARCHAR(10)');
        $this->addSql('CREATE INDEX idx_album_final_visibility ON koi_album (final_visibility)');
        $this->addSql('UPDATE koi_album SET parent_visibility=visibility, final_visibility=visibility WHERE parent_id IS NOT NULL');
        $this->addSql('UPDATE koi_album SET final_visibility=visibility WHERE parent_id IS NULL');
        $this->addSql('ALTER TABLE koi_album ALTER final_visibility SET NOT NULL');

        $this->addSql('DROP INDEX idx_collection_visibility');
        $this->addSql('ALTER TABLE koi_collection ADD parent_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_collection ADD final_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_collection ALTER visibility TYPE VARCHAR(10)');
        $this->addSql('CREATE INDEX idx_collection_final_visibility ON koi_collection (final_visibility)');
        $this->addSql('UPDATE koi_collection SET parent_visibility=visibility, final_visibility=visibility WHERE parent_id IS NOT NULL');
        $this->addSql('UPDATE koi_collection SET final_visibility=visibility WHERE parent_id IS NULL');
        $this->addSql('ALTER TABLE koi_collection ALTER final_visibility SET NOT NULL');

        $this->addSql('DROP INDEX idx_datum_final_visibility');
        $this->addSql('ALTER TABLE koi_datum DROP visibility');

        $this->addSql('DROP INDEX idx_item_visibility');
        $this->addSql('ALTER TABLE koi_item ADD parent_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_item ADD final_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_item ALTER visibility TYPE VARCHAR(10)');
        $this->addSql('CREATE INDEX idx_item_final_visibility ON koi_item (final_visibility)');
        $this->addSql('UPDATE koi_item SET parent_visibility=visibility, final_visibility=visibility');
        $this->addSql('ALTER TABLE koi_item ALTER final_visibility SET NOT NULL');

        $this->addSql('ALTER TABLE koi_log ALTER type TYPE VARCHAR(6)');
        $this->addSql('ALTER TABLE koi_log ALTER object_id TYPE VARCHAR(36)');
        $this->addSql('ALTER TABLE koi_log ALTER object_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_log.object_id IS NULL');

        $this->addSql('DROP INDEX idx_photo_visibility');
        $this->addSql('ALTER TABLE koi_photo ADD parent_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_photo ADD final_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_photo ALTER visibility TYPE VARCHAR(10)');
        $this->addSql('CREATE INDEX idx_photo_final_visibility ON koi_photo (final_visibility)');
        $this->addSql('UPDATE koi_photo SET parent_visibility=visibility, final_visibility=visibility');
        $this->addSql('ALTER TABLE koi_photo ALTER final_visibility SET NOT NULL');

        $this->addSql('ALTER TABLE koi_tag ALTER visibility TYPE VARCHAR(10)');

        $this->addSql('ALTER TABLE koi_user ALTER visibility TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_user ALTER timezone TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE koi_user ALTER date_format TYPE VARCHAR(10)');

        $this->addSql('DROP INDEX idx_wish_visibility');
        $this->addSql('ALTER TABLE koi_wish ADD parent_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_wish ADD final_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_wish ALTER visibility TYPE VARCHAR(10)');
        $this->addSql('CREATE INDEX idx_wish_final_visibility ON koi_wish (final_visibility)');
        $this->addSql('UPDATE koi_wish SET parent_visibility=visibility, final_visibility=visibility');
        $this->addSql('ALTER TABLE koi_wish ALTER final_visibility SET NOT NULL');

        $this->addSql('DROP INDEX idx_wishlist_visibility');
        $this->addSql('ALTER TABLE koi_wishlist ADD parent_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_wishlist ADD final_visibility VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_wishlist ALTER visibility TYPE VARCHAR(10)');
        $this->addSql('CREATE INDEX idx_wishlist_final_visibility ON koi_wishlist (final_visibility)');
        $this->addSql('UPDATE koi_wishlist SET parent_visibility=visibility, final_visibility=visibility WHERE parent_id IS NOT NULL');
        $this->addSql('UPDATE koi_wishlist SET final_visibility=visibility WHERE parent_id IS NULL');
        $this->addSql('ALTER TABLE koi_wishlist ALTER final_visibility SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
