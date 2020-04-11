<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200411112345 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Remove property `color` for `koi_wish` table and regenerate all UUID types';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE koi_album_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_collection_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_datum_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_field_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_item_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_loan_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_log_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_medium_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_photo_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_template_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_wish_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE koi_wishlist_id_seq CASCADE');
        $this->addSql('ALTER TABLE koi_wishlist ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_wishlist ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wishlist ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_wishlist ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wishlist ALTER parent_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_wishlist ALTER parent_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wishlist ALTER image_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_wishlist ALTER image_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_user ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_user ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_user ALTER avatar_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_user ALTER avatar_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_user.avatar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_template ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_template ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_template ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_template ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_template.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_template.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_log ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_log ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_log ALTER object_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_log ALTER object_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_log.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_log.object_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_loan ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_loan ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_loan ALTER item_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_loan ALTER item_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_loan ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_loan ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_loan.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_loan.item_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_loan.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_wish DROP color');
        $this->addSql('ALTER TABLE koi_wish ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_wish ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wish ALTER wishlist_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_wish ALTER wishlist_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wish ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_wish ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wish ALTER image_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_wish ALTER image_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_wish.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wish.wishlist_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wish.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wish.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_field ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_field ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_field ALTER template_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_field ALTER template_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_field.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_field.template_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_photo ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_photo ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_photo ALTER image_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_photo ALTER image_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_photo ALTER album_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_photo ALTER album_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_photo ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_photo ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_photo.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.album_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_item ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_item ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item ALTER collection_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_item ALTER collection_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_item ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item ALTER template_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_item ALTER template_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item ALTER image_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_item ALTER image_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_item.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item.collection_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item.template_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_item_tag ALTER item_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_item_tag ALTER item_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item_tag ALTER tag_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_item_tag ALTER tag_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_item_tag.item_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item_tag.tag_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_collection ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_collection ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_collection ALTER parent_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_collection ALTER parent_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_collection ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_collection ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_collection ALTER image_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_collection ALTER image_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_collection.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_collection.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_collection.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_collection.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_album ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_album ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_album ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_album ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_album.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_album.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_tag ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_tag ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_tag ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_tag ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_tag ALTER image_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_tag ALTER image_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_tag.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_tag.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_tag.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_datum ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_datum ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_datum ALTER item_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_datum ALTER item_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_datum ALTER image_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_datum ALTER image_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_datum ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_datum ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_datum.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_datum.item_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_datum.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_datum.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_medium ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE koi_medium ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_medium ALTER owner_id TYPE UUID');
        $this->addSql('ALTER TABLE koi_medium ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_medium.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_medium.owner_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
