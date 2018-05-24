<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20180524154516 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_datum ALTER type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE koi_datum ALTER type DROP DEFAULT');

        $this->addSql('UPDATE koi_datum d SET type = \'text\' WHERE type = \'1\'');
        $this->addSql('UPDATE koi_datum d SET type = \'sign\' WHERE type = \'2\'');
        $this->addSql('UPDATE koi_datum d SET type = \'image\' WHERE type = \'3\'');

        $this->addSql('UPDATE koi_log l SET payload = replace(payload, \'"datum_type":1\', \'"datum_type":"text"\')');
        $this->addSql('UPDATE koi_log l SET payload = replace(payload, \'"datum_type":2\', \'"datum_type":"sign"\')');
        $this->addSql('UPDATE koi_log l SET payload = replace(payload, \'"datum_type":3\', \'"datum_type":"image"\')');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
