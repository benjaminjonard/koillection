<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220725195801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Change all datetimes to immutable datetimes';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_album ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_album ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_album ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_album ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_album.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_album.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_choice_list ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_choice_list ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_choice_list ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_choice_list ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_choice_list.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_choice_list.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_collection ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_collection ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_collection ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_collection ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_collection.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_collection.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_datum ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_datum ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_datum ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_datum ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_datum.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_datum.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_inventory ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_inventory ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_inventory ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_inventory ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_inventory.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_inventory.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_item ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_item ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_item ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_item.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_item.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_loan ALTER lent_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_loan ALTER lent_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_loan ALTER returned_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_loan ALTER returned_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_loan.lent_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_loan.returned_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_log DROP payload');
        $this->addSql('ALTER TABLE koi_log ALTER logged_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_log ALTER logged_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_log.logged_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_photo ALTER taken_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_photo ALTER taken_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_photo ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_photo ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_photo ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_photo ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_photo.taken_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_tag ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_tag ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_tag ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_tag ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_tag.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_tag.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_tag_category ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_tag_category ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_tag_category ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_tag_category ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_tag_category.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_tag_category.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_template ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_template ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_template ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_template ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_template.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_template.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_user ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_user ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_user ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_user ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_user.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_user.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_wish ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_wish ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wish ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_wish ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_wish.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_wish.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_wishlist ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_wishlist ALTER created_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wishlist ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_wishlist ALTER updated_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE koi_user ALTER automatic_dark_mode_start_at TYPE TIME(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_user ALTER automatic_dark_mode_start_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_user ALTER automatic_dark_mode_end_at TYPE TIME(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_user ALTER automatic_dark_mode_end_at DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_user.automatic_dark_mode_start_at IS \'(DC2Type:time_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_user.automatic_dark_mode_end_at IS \'(DC2Type:time_immutable)\'');
        $this->addSql('ALTER TABLE koi_user ALTER last_date_of_activity TYPE DATE');
        $this->addSql('ALTER TABLE koi_user ALTER last_date_of_activity DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN koi_user.last_date_of_activity IS \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
