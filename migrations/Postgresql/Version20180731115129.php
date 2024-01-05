<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20180731115129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] First init.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE koi_collection (id UUID NOT NULL, parent_id UUID DEFAULT NULL, owner_id UUID DEFAULT NULL, image_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, children_title VARCHAR(255) DEFAULT NULL, items_title VARCHAR(255) DEFAULT NULL, color VARCHAR(6) NOT NULL, seen_counter INT NOT NULL, visibility VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7AA7B057727ACA70 ON koi_collection (parent_id)');
        $this->addSql('CREATE INDEX IDX_7AA7B0577E3C61F9 ON koi_collection (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7AA7B0573DA5256D ON koi_collection (image_id)');
        $this->addSql('COMMENT ON COLUMN koi_collection.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_collection.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_collection.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_collection.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_log (id UUID NOT NULL, user_id UUID DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, object_id UUID NOT NULL, object_label VARCHAR(255) NOT NULL, object_class VARCHAR(255) NOT NULL, payload TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9A4DC1F1A76ED395 ON koi_log (user_id)');
        $this->addSql('COMMENT ON COLUMN koi_log.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_log.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_log.object_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_album (id UUID NOT NULL, owner_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, color VARCHAR(6) NOT NULL, seen_counter INT NOT NULL, visibility VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2DB8938A7E3C61F9 ON koi_album (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_album.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_album.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_user (id UUID NOT NULL, avatar_id UUID DEFAULT NULL, username VARCHAR(32) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, roles TEXT NOT NULL, theme VARCHAR(255) NOT NULL, currency VARCHAR(3) NOT NULL, locale VARCHAR(2) NOT NULL, timezone VARCHAR(255) DEFAULT NULL, disk_space_used INT NOT NULL, disk_space_allowed INT NOT NULL, visibility VARCHAR(255) NOT NULL, last_date_of_activity DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC325055F85E0677 ON koi_user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC325055E7927C74 ON koi_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC32505586383B10 ON koi_user (avatar_id)');
        $this->addSql('COMMENT ON COLUMN koi_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_user.avatar_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_user.roles IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE koi_wish (id UUID NOT NULL, wishlist_id UUID DEFAULT NULL, owner_id UUID DEFAULT NULL, image_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, url TEXT DEFAULT NULL, price VARCHAR(255) DEFAULT NULL, currency VARCHAR(6) DEFAULT NULL, color VARCHAR(6) NOT NULL, comment TEXT DEFAULT NULL, visibility VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F670F2D5FB8E54CD ON koi_wish (wishlist_id)');
        $this->addSql('CREATE INDEX IDX_F670F2D57E3C61F9 ON koi_wish (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F670F2D53DA5256D ON koi_wish (image_id)');
        $this->addSql('COMMENT ON COLUMN koi_wish.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wish.wishlist_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wish.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wish.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_medium (id UUID NOT NULL, owner_id UUID DEFAULT NULL, type SMALLINT NOT NULL, filename VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, thumbnail_path VARCHAR(255) DEFAULT NULL, size INT NOT NULL, thumbnail_size INT DEFAULT NULL, mimetype VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_24DF1F5E7E3C61F9 ON koi_medium (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_medium.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_medium.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_tag (id UUID NOT NULL, image_id UUID DEFAULT NULL, owner_id UUID DEFAULT NULL, label VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, seen_counter INT NOT NULL, visibility VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16FB1EB73DA5256D ON koi_tag (image_id)');
        $this->addSql('CREATE INDEX IDX_16FB1EB77E3C61F9 ON koi_tag (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_tag.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_tag.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_tag.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_item (id UUID NOT NULL, collection_id UUID DEFAULT NULL, owner_id UUID DEFAULT NULL, template_id UUID DEFAULT NULL, image_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, quantity INT NOT NULL, seen_counter INT NOT NULL, visibility VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3EBAA302514956FD ON koi_item (collection_id)');
        $this->addSql('CREATE INDEX IDX_3EBAA3027E3C61F9 ON koi_item (owner_id)');
        $this->addSql('CREATE INDEX IDX_3EBAA3025DA0FB8 ON koi_item (template_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EBAA3023DA5256D ON koi_item (image_id)');
        $this->addSql('COMMENT ON COLUMN koi_item.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item.collection_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item.template_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_item_tag (item_id UUID NOT NULL, tag_id UUID NOT NULL, PRIMARY KEY(item_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_E09EDE52126F525E ON koi_item_tag (item_id)');
        $this->addSql('CREATE INDEX IDX_E09EDE52BAD26311 ON koi_item_tag (tag_id)');
        $this->addSql('COMMENT ON COLUMN koi_item_tag.item_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_item_tag.tag_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_loan (id UUID NOT NULL, item_id UUID DEFAULT NULL, owner_id UUID DEFAULT NULL, lent_to VARCHAR(255) NOT NULL, lent_at DATE NOT NULL, returned_at DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E4728B1F126F525E ON koi_loan (item_id)');
        $this->addSql('CREATE INDEX IDX_E4728B1F7E3C61F9 ON koi_loan (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_loan.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_loan.item_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_loan.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_wishlist (id UUID NOT NULL, owner_id UUID DEFAULT NULL, parent_id UUID DEFAULT NULL, image_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(6) NOT NULL, seen_counter INT NOT NULL, visibility VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98E338D27E3C61F9 ON koi_wishlist (owner_id)');
        $this->addSql('CREATE INDEX IDX_98E338D2727ACA70 ON koi_wishlist (parent_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_98E338D23DA5256D ON koi_wishlist (image_id)');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_photo (id UUID NOT NULL, album_id UUID DEFAULT NULL, owner_id UUID DEFAULT NULL, image_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, comment TEXT DEFAULT NULL, place VARCHAR(255) DEFAULT NULL, taken_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, visibility VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9779D11137ABCF ON koi_photo (album_id)');
        $this->addSql('CREATE INDEX IDX_9779D17E3C61F9 ON koi_photo (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9779D13DA5256D ON koi_photo (image_id)');
        $this->addSql('COMMENT ON COLUMN koi_photo.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.album_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_template (id UUID NOT NULL, owner_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_93620D607E3C61F9 ON koi_template (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_template.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_template.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_datum (id UUID NOT NULL, item_id UUID DEFAULT NULL, image_id UUID DEFAULT NULL, owner_id UUID DEFAULT NULL, type VARCHAR(255) NOT NULL, label VARCHAR(255) DEFAULT NULL, value TEXT DEFAULT NULL, position INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F991BE5126F525E ON koi_datum (item_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F991BE53DA5256D ON koi_datum (image_id)');
        $this->addSql('CREATE INDEX IDX_F991BE57E3C61F9 ON koi_datum (owner_id)');
        $this->addSql('COMMENT ON COLUMN koi_datum.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_datum.item_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_datum.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_datum.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE koi_field (id UUID NOT NULL, template_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, position INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4FD5B8915DA0FB8 ON koi_field (template_id)');
        $this->addSql('COMMENT ON COLUMN koi_field.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_field.template_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B057727ACA70 FOREIGN KEY (parent_id) REFERENCES koi_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B0577E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B0573DA5256D FOREIGN KEY (image_id) REFERENCES koi_medium (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_log ADD CONSTRAINT FK_9A4DC1F1A76ED395 FOREIGN KEY (user_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_album ADD CONSTRAINT FK_2DB8938A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_user ADD CONSTRAINT FK_AC32505586383B10 FOREIGN KEY (avatar_id) REFERENCES koi_medium (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wish ADD CONSTRAINT FK_F670F2D5FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES koi_wishlist (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wish ADD CONSTRAINT FK_F670F2D57E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wish ADD CONSTRAINT FK_F670F2D53DA5256D FOREIGN KEY (image_id) REFERENCES koi_medium (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_medium ADD CONSTRAINT FK_24DF1F5E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_tag ADD CONSTRAINT FK_16FB1EB73DA5256D FOREIGN KEY (image_id) REFERENCES koi_medium (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_tag ADD CONSTRAINT FK_16FB1EB77E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item ADD CONSTRAINT FK_3EBAA302514956FD FOREIGN KEY (collection_id) REFERENCES koi_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item ADD CONSTRAINT FK_3EBAA3027E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item ADD CONSTRAINT FK_3EBAA3025DA0FB8 FOREIGN KEY (template_id) REFERENCES koi_template (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item ADD CONSTRAINT FK_3EBAA3023DA5256D FOREIGN KEY (image_id) REFERENCES koi_medium (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item_tag ADD CONSTRAINT FK_E09EDE52126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item_tag ADD CONSTRAINT FK_E09EDE52BAD26311 FOREIGN KEY (tag_id) REFERENCES koi_tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_loan ADD CONSTRAINT FK_E4728B1F126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_loan ADD CONSTRAINT FK_E4728B1F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wishlist ADD CONSTRAINT FK_98E338D27E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wishlist ADD CONSTRAINT FK_98E338D2727ACA70 FOREIGN KEY (parent_id) REFERENCES koi_wishlist (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wishlist ADD CONSTRAINT FK_98E338D23DA5256D FOREIGN KEY (image_id) REFERENCES koi_medium (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_photo ADD CONSTRAINT FK_9779D11137ABCF FOREIGN KEY (album_id) REFERENCES koi_album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_photo ADD CONSTRAINT FK_9779D17E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_photo ADD CONSTRAINT FK_9779D13DA5256D FOREIGN KEY (image_id) REFERENCES koi_medium (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_template ADD CONSTRAINT FK_93620D607E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE5126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE53DA5256D FOREIGN KEY (image_id) REFERENCES koi_medium (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE57E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_field ADD CONSTRAINT FK_4FD5B8915DA0FB8 FOREIGN KEY (template_id) REFERENCES koi_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
