<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20180507140138 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_wish DROP CONSTRAINT FK_F670F2D5FB8E54CD');
        $this->addSql('ALTER TABLE koi_wish ADD CONSTRAINT FK_F670F2D5FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES koi_wishlist (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_item DROP CONSTRAINT FK_3EBAA302514956FD');
        $this->addSql('ALTER TABLE koi_item ADD CONSTRAINT FK_3EBAA302514956FD FOREIGN KEY (collection_id) REFERENCES koi_collection (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_loan DROP CONSTRAINT FK_E4728B1F126F525E');
        $this->addSql('ALTER TABLE koi_loan ADD CONSTRAINT FK_E4728B1F126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_photo DROP CONSTRAINT FK_9779D11137ABCF');
        $this->addSql('ALTER TABLE koi_photo ADD CONSTRAINT FK_9779D11137ABCF FOREIGN KEY (album_id) REFERENCES koi_album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_datum DROP CONSTRAINT FK_F991BE5126F525E');
        $this->addSql('ALTER TABLE koi_datum ADD CONSTRAINT FK_F991BE5126F525E FOREIGN KEY (item_id) REFERENCES koi_item (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
