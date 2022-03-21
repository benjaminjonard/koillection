<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211207185256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Switch to Symfony UUIDs';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_album CHANGE id id CHAR(36) NOT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL, CHANGE parent_id parent_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_collection CHANGE id id CHAR(36) NOT NULL, CHANGE parent_id parent_id CHAR(36) DEFAULT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_datum CHANGE id id CHAR(36) NOT NULL, CHANGE item_id item_id CHAR(36) DEFAULT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL, CHANGE collection_id collection_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_field CHANGE id id CHAR(36) NOT NULL, CHANGE template_id template_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_inventory CHANGE id id CHAR(36) NOT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_item CHANGE id id CHAR(36) NOT NULL, CHANGE collection_id collection_id CHAR(36) DEFAULT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_item_tag CHANGE item_id item_id CHAR(36) NOT NULL, CHANGE tag_id tag_id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE koi_item_related_item CHANGE item_id item_id CHAR(36) NOT NULL, CHANGE related_item_id related_item_id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE koi_loan CHANGE id id CHAR(36) NOT NULL, CHANGE item_id item_id CHAR(36) DEFAULT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_log CHANGE id id CHAR(36) NOT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_photo CHANGE id id CHAR(36) NOT NULL, CHANGE album_id album_id CHAR(36) DEFAULT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_tag CHANGE id id CHAR(36) NOT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL, CHANGE category_id category_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_tag_category CHANGE id id CHAR(36) NOT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_template CHANGE id id CHAR(36) NOT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_user CHANGE id id CHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE koi_wish CHANGE id id CHAR(36) NOT NULL, CHANGE wishlist_id wishlist_id CHAR(36) DEFAULT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_wishlist CHANGE id id CHAR(36) NOT NULL, CHANGE owner_id owner_id CHAR(36) DEFAULT NULL, CHANGE parent_id parent_id CHAR(36) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
