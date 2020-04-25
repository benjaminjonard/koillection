<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200425201544 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Remove `koi_image` table';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_wish DROP CONSTRAINT fk_f670f2d53da5256d');
        $this->addSql('ALTER TABLE koi_collection DROP CONSTRAINT fk_7aa7b0573da5256d');
        $this->addSql('ALTER TABLE koi_wishlist DROP CONSTRAINT fk_98e338d23da5256d');
        $this->addSql('ALTER TABLE koi_photo DROP CONSTRAINT fk_9779d13da5256d');
        $this->addSql('ALTER TABLE koi_album DROP CONSTRAINT fk_2db8938a3da5256d');
        $this->addSql('ALTER TABLE koi_datum DROP CONSTRAINT fk_f991be53da5256d');
        $this->addSql('ALTER TABLE koi_item DROP CONSTRAINT fk_3ebaa3023da5256d');
        $this->addSql('ALTER TABLE koi_tag DROP CONSTRAINT fk_16fb1eb73da5256d');
        $this->addSql('ALTER TABLE koi_user DROP CONSTRAINT fk_ac32505586383b10');
        $this->addSql('DROP TABLE koi_image');
        $this->addSql('DROP INDEX uniq_98e338d23da5256d');
        $this->addSql('ALTER TABLE koi_wishlist ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_wishlist DROP image_id');
        $this->addSql('DROP INDEX uniq_ac32505586383b10');
        $this->addSql('ALTER TABLE koi_user ADD avatar VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_user DROP avatar_id');
        $this->addSql('ALTER TABLE koi_user DROP disk_space_used');
        $this->addSql('DROP INDEX uniq_f670f2d53da5256d');
        $this->addSql('ALTER TABLE koi_wish ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_wish ADD image_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_wish DROP image_id');
        $this->addSql('DROP INDEX uniq_9779d13da5256d');
        $this->addSql('ALTER TABLE koi_photo ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_photo ADD image_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_photo DROP image_id');
        $this->addSql('DROP INDEX uniq_3ebaa3023da5256d');
        $this->addSql('ALTER TABLE koi_item ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_item ADD image_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_item ADD image_medium_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_item DROP image_id');
        $this->addSql('DROP INDEX uniq_7aa7b0573da5256d');
        $this->addSql('ALTER TABLE koi_collection ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_collection DROP image_id');
        $this->addSql('DROP INDEX uniq_2db8938a3da5256d');
        $this->addSql('ALTER TABLE koi_album ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_album DROP image_id');
        $this->addSql('DROP INDEX uniq_16fb1eb73da5256d');
        $this->addSql('ALTER TABLE koi_tag ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_tag ADD image_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_tag DROP image_id');
        $this->addSql('DROP INDEX uniq_f991be53da5256d');
        $this->addSql('ALTER TABLE koi_datum ADD image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_datum ADD image_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_datum ADD image_medium_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_datum DROP image_id');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
