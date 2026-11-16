<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261116185840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Remove legacy DC2Type comments on date columns';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Postgresql migration only. Skipped.');

        $this->addSql('COMMENT ON COLUMN koi_album.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_album.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_choice_list.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_choice_list.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_collection.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_collection.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_configuration.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_configuration.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_datum.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_datum.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_display_configuration.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_display_configuration.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_inventory.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_inventory.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_item.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_item.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_loan.lent_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_loan.returned_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_log.logged_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.taken_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_photo.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_scraper.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_scraper.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_tag.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_tag.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_tag_category.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_tag_category.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_template.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_template.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_user.last_date_of_activity IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_user.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_user.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_wish.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_wish.updated_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.created_at IS \'\'');
        $this->addSql('COMMENT ON COLUMN koi_wishlist.updated_at IS \'\'');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
