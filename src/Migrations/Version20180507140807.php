<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20180507140807 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_log ADD user_id UUID DEFAULT NULL');

        //Replace the username with the user's id
        $this->addSql('UPDATE koi_log SET user_id = (DISTINCT SELECT u.id FROM koi_user u WHERE u.username = username)');

        $this->addSql('ALTER TABLE koi_log DROP username');
        $this->addSql('COMMENT ON COLUMN koi_log.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_log ADD CONSTRAINT FK_9A4DC1F1A76ED395 FOREIGN KEY (user_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9A4DC1F1A76ED395 ON koi_log (user_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
