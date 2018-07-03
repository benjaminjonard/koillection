<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20180703153506 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE koi_connection');
        $this->addSql('ALTER TABLE koi_item DROP CONSTRAINT FK_3EBAA3025DA0FB8');
        $this->addSql('ALTER TABLE koi_item ALTER seen_counter DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item ADD CONSTRAINT FK_3EBAA3025DA0FB8 FOREIGN KEY (template_id) REFERENCES koi_template (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_collection ALTER seen_counter DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_album ALTER seen_counter DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_tag ALTER seen_counter DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wishlist ALTER seen_counter DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
