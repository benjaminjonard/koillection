<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20180524143002 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_user ADD last_date_of_activity DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_user DROP last_action_at');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
