<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version201805222601 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('UPDATE koi_field d SET type = \'text\' WHERE type = \'1\'');
        $this->addSql('UPDATE koi_field d SET type = \'sign\' WHERE type = \'2\'');
        $this->addSql('UPDATE koi_field d SET type = \'image\' WHERE type = \'3\'');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
