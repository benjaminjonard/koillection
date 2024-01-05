<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211207190313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Switch to Symfony UUIDs';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_album DROP CONSTRAINT FK_2DB8938A7E3C61F9');
        $this->addSql('ALTER TABLE koi_album DROP CONSTRAINT FK_2DB8938A727ACA70');
        $this->addSql('ALTER TABLE koi_collection DROP CONSTRAINT FK_7AA7B057727ACA70');
        $this->addSql('ALTER TABLE koi_collection DROP CONSTRAINT FK_7AA7B0577E3C61F9');
        $this->addSql('ALTER TABLE koi_datum DROP CONSTRAINT FK_F991BE5126F525E');
        $this->addSql('ALTER TABLE koi_datum DROP CONSTRAINT FK_F991BE5514956FD');
        $this->addSql('ALTER TABLE koi_datum DROP CONSTRAINT FK_F991BE57E3C61F9');
        $this->addSql('ALTER TABLE koi_field DROP CONSTRAINT FK_4FD5B8915DA0FB8');
        $this->addSql('ALTER TABLE koi_inventory DROP CONSTRAINT FK_882AFBE67E3C61F9');
        $this->addSql('ALTER TABLE koi_item DROP CONSTRAINT FK_3EBAA302514956FD');
        $this->addSql('ALTER TABLE koi_item DROP CONSTRAINT FK_3EBAA3027E3C61F9');
        $this->addSql('ALTER TABLE koi_item_tag DROP CONSTRAINT FK_E09EDE52126F525E');
        $this->addSql('ALTER TABLE koi_item_tag DROP CONSTRAINT FK_E09EDE52BAD26311');
        $this->addSql('ALTER TABLE koi_item_related_item DROP CONSTRAINT FK_A78A49D126F525E');
        $this->addSql('ALTER TABLE koi_item_related_item DROP CONSTRAINT FK_A78A49D2D7698FB');
        $this->addSql('ALTER TABLE koi_loan DROP CONSTRAINT FK_E4728B1F126F525E');
        $this->addSql('ALTER TABLE koi_loan DROP CONSTRAINT FK_E4728B1F7E3C61F9');
        $this->addSql('ALTER TABLE koi_log DROP CONSTRAINT FK_9A4DC1F17E3C61F9');
        $this->addSql('ALTER TABLE koi_photo DROP CONSTRAINT FK_9779D11137ABCF');
        $this->addSql('ALTER TABLE koi_photo DROP CONSTRAINT FK_9779D17E3C61F9');
        $this->addSql('ALTER TABLE koi_tag DROP CONSTRAINT FK_16FB1EB77E3C61F9');
        $this->addSql('ALTER TABLE koi_tag DROP CONSTRAINT FK_16FB1EB712469DE2');
        $this->addSql('ALTER TABLE koi_tag_category DROP CONSTRAINT FK_DE4E5D497E3C61F9');
        $this->addSql('ALTER TABLE koi_template DROP CONSTRAINT FK_93620D607E3C61F9');
        $this->addSql('ALTER TABLE koi_wish DROP CONSTRAINT FK_F670F2D5FB8E54CD');
        $this->addSql('ALTER TABLE koi_wish DROP CONSTRAINT FK_F670F2D57E3C61F9');
        $this->addSql('ALTER TABLE koi_wishlist DROP CONSTRAINT FK_98E338D27E3C61F9');
        $this->addSql('ALTER TABLE koi_wishlist DROP CONSTRAINT FK_98E338D2727ACA70');

        $this->addSql('ALTER TABLE koi_album ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_album ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_album ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_album ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_album ALTER parent_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_album ALTER parent_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_album.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_album.owner_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_album.parent_id IS NULL');
        $this->addSql('ALTER TABLE koi_collection ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_collection ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_collection ALTER parent_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_collection ALTER parent_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_collection ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_collection ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_collection.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_collection.parent_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_collection.owner_id IS NULL');
        $this->addSql('ALTER TABLE koi_datum ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_datum ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_datum ALTER item_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_datum ALTER item_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_datum ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_datum ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_datum ALTER collection_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_datum ALTER collection_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_datum.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_datum.item_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_datum.owner_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_datum.collection_id IS NULL');
        $this->addSql('ALTER TABLE koi_field ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_field ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_field ALTER template_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_field ALTER template_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_field.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_field.template_id IS NULL');
        $this->addSql('ALTER TABLE koi_inventory ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_inventory ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_inventory ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_inventory ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_inventory.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_inventory.owner_id IS NULL');
        $this->addSql('ALTER TABLE koi_item ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_item ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item ALTER collection_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_item ALTER collection_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_item ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_item.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_item.collection_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_item.owner_id IS NULL');
        $this->addSql('ALTER TABLE koi_item_tag ALTER item_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_item_tag ALTER item_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item_tag ALTER tag_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_item_tag ALTER tag_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_item_tag.item_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_item_tag.tag_id IS NULL');
        $this->addSql('ALTER TABLE koi_item_related_item ALTER item_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_item_related_item ALTER item_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item_related_item ALTER related_item_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_item_related_item ALTER related_item_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_item_related_item.item_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_item_related_item.related_item_id IS NULL');
        $this->addSql('ALTER TABLE koi_loan ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_loan ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_loan ALTER item_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_loan ALTER item_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_loan ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_loan ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_loan.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_loan.item_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_loan.owner_id IS NULL');
        $this->addSql('ALTER TABLE koi_log ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_log ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_log ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_log ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_log.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_log.owner_id IS NULL');
        $this->addSql('ALTER TABLE koi_photo ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_photo ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_photo ALTER album_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_photo ALTER album_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_photo ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_photo ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_photo.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_photo.album_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_photo.owner_id IS NULL');
        $this->addSql('ALTER TABLE koi_tag ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_tag ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_tag ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_tag ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_tag ALTER category_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_tag ALTER category_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_tag.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_tag.owner_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_tag.category_id IS NULL');
        $this->addSql('ALTER TABLE koi_tag_category ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_tag_category ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_tag_category ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_tag_category ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_tag_category.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_tag_category.owner_id IS NULL');
        $this->addSql('ALTER TABLE koi_template ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_template ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_template ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_template ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_template.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_template.owner_id IS NULL');
        $this->addSql('ALTER TABLE koi_user ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_user ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_user.id IS NULL');
        $this->addSql('ALTER TABLE koi_wish ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_wish ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wish ALTER wishlist_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_wish ALTER wishlist_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wish ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_wish ALTER owner_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_wish.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_wish.wishlist_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_wish.owner_id IS NULL');
        $this->addSql('ALTER TABLE koi_wishlist ALTER id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_wishlist ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wishlist ALTER owner_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_wishlist ALTER owner_id DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wishlist ALTER parent_id TYPE CHAR(36)');
        $this->addSql('ALTER TABLE koi_wishlist ALTER parent_id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.owner_id IS NULL');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.parent_id IS NULL');

        $this->addSql('ALTER TABLE koi_album ADD CONSTRAINT FK_2DB8938A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_album ADD CONSTRAINT FK_2DB8938A727ACA70 FOREIGN KEY (parent_id) REFERENCES koi_album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B057727ACA70 FOREIGN KEY (parent_id) REFERENCES koi_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B0577E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE5126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE5514956FD FOREIGN KEY (collection_id) REFERENCES koi_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE57E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_field ADD CONSTRAINT FK_4FD5B8915DA0FB8 FOREIGN KEY (template_id) REFERENCES koi_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_inventory ADD CONSTRAINT FK_882AFBE67E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item ADD CONSTRAINT FK_3EBAA302514956FD FOREIGN KEY (collection_id) REFERENCES koi_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item ADD CONSTRAINT FK_3EBAA3027E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item_tag ADD CONSTRAINT FK_E09EDE52126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item_tag ADD CONSTRAINT FK_E09EDE52BAD26311 FOREIGN KEY (tag_id) REFERENCES koi_tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item_related_item ADD CONSTRAINT FK_A78A49D126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item_related_item ADD CONSTRAINT FK_A78A49D2D7698FB FOREIGN KEY (related_item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_loan ADD CONSTRAINT FK_E4728B1F126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_loan ADD CONSTRAINT FK_E4728B1F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_log ADD CONSTRAINT FK_9A4DC1F17E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_photo ADD CONSTRAINT FK_9779D11137ABCF FOREIGN KEY (album_id) REFERENCES koi_album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_photo ADD CONSTRAINT FK_9779D17E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_tag ADD CONSTRAINT FK_16FB1EB77E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_tag ADD CONSTRAINT FK_16FB1EB712469DE2 FOREIGN KEY (category_id) REFERENCES koi_tag_category (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_tag_category ADD CONSTRAINT FK_DE4E5D497E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_template ADD CONSTRAINT FK_93620D607E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wish ADD CONSTRAINT FK_F670F2D5FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES koi_wishlist (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wish ADD CONSTRAINT FK_F670F2D57E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wishlist ADD CONSTRAINT FK_98E338D27E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wishlist ADD CONSTRAINT FK_98E338D2727ACA70 FOREIGN KEY (parent_id) REFERENCES koi_wishlist (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
