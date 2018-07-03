<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20180703104233 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD seen_counter INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE koi_tag ADD seen_counter INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE koi_item ADD seen_counter INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE koi_album ADD seen_counter INT NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE koi_wishlist ADD seen_counter INT NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
