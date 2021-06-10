<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200413120102 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '[Postgresql] Rename `koi_medium` to `koi_image`';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_wishlist DROP CONSTRAINT fk_98e338d23da5256d');
        $this->addSql('ALTER TABLE koi_wish DROP CONSTRAINT fk_f670f2d53da5256d');
        $this->addSql('ALTER TABLE koi_photo DROP CONSTRAINT fk_9779d13da5256d');
        $this->addSql('ALTER TABLE koi_item DROP CONSTRAINT fk_3ebaa3023da5256d');
        $this->addSql('ALTER TABLE koi_collection DROP CONSTRAINT fk_7aa7b0573da5256d');
        $this->addSql('ALTER TABLE koi_tag DROP CONSTRAINT fk_16fb1eb73da5256d');
        $this->addSql('ALTER TABLE koi_datum DROP CONSTRAINT fk_f991be53da5256d');
        $this->addSql('ALTER TABLE koi_user DROP CONSTRAINT fk_ac32505586383b10');

        $this->addSql('ALTER TABLE koi_medium RENAME TO koi_image');
        $this->addSql('ALTER TABLE koi_image DROP type');
        $this->addSql('ALTER INDEX idx_24df1f5e7e3c61f9 RENAME TO IDX_D11DF9967E3C61F9');

        $this->addSql('ALTER TABLE koi_wishlist ADD CONSTRAINT FK_98E338D23DA5256D FOREIGN KEY (image_id) REFERENCES koi_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_user ADD CONSTRAINT FK_AC32505586383B10 FOREIGN KEY (avatar_id) REFERENCES koi_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_wish ADD CONSTRAINT FK_F670F2D53DA5256D FOREIGN KEY (image_id) REFERENCES koi_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_photo ADD CONSTRAINT FK_9779D13DA5256D FOREIGN KEY (image_id) REFERENCES koi_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item ADD CONSTRAINT FK_3EBAA3023DA5256D FOREIGN KEY (image_id) REFERENCES koi_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B0573DA5256D FOREIGN KEY (image_id) REFERENCES koi_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_tag ADD CONSTRAINT FK_16FB1EB73DA5256D FOREIGN KEY (image_id) REFERENCES koi_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE53DA5256D FOREIGN KEY (image_id) REFERENCES koi_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
