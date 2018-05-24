<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20180524161825 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_user ALTER visibility TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE koi_user ALTER visibility DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_album ALTER visibility TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE koi_album ALTER visibility DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_collection ALTER visibility TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE koi_collection ALTER visibility DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_item ALTER visibility TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE koi_item ALTER visibility DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_photo ALTER visibility TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE koi_photo ALTER visibility DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_tag ALTER visibility TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE koi_tag ALTER visibility DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wish ALTER visibility TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE koi_wish ALTER visibility DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wishlist ALTER visibility TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE koi_wishlist ALTER visibility DROP DEFAULT');

        $this->addSql('UPDATE koi_user u SET visibility = \'public\' WHERE visibility = \'1\'');
        $this->addSql('UPDATE koi_user u SET visibility = \'private\' WHERE visibility = \'2\'');
        $this->addSql('UPDATE koi_album a SET visibility = \'public\' WHERE visibility = \'1\'');
        $this->addSql('UPDATE koi_album a SET visibility = \'private\' WHERE visibility = \'2\'');
        $this->addSql('UPDATE koi_collection c SET visibility = \'public\' WHERE visibility = \'1\'');
        $this->addSql('UPDATE koi_collection c SET visibility = \'private\' WHERE visibility = \'2\'');
        $this->addSql('UPDATE koi_item i SET visibility = \'public\' WHERE visibility = \'1\'');
        $this->addSql('UPDATE koi_item i SET visibility = \'private\' WHERE visibility = \'2\'');
        $this->addSql('UPDATE koi_photo p SET visibility = \'public\' WHERE visibility = \'1\'');
        $this->addSql('UPDATE koi_photo p SET visibility = \'private\' WHERE visibility = \'2\'');
        $this->addSql('UPDATE koi_tag t SET visibility = \'public\' WHERE visibility = \'1\'');
        $this->addSql('UPDATE koi_tag t SET visibility = \'private\' WHERE visibility = \'2\'');
        $this->addSql('UPDATE koi_wish w SET visibility = \'public\' WHERE visibility = \'1\'');
        $this->addSql('UPDATE koi_wish w SET visibility = \'private\' WHERE visibility = \'2\'');
        $this->addSql('UPDATE koi_wishlist w SET visibility = \'public\' WHERE visibility = \'1\'');
        $this->addSql('UPDATE koi_wishlist w SET visibility = \'private\' WHERE visibility = \'2\'');


        $this->addSql('UPDATE koi_log l SET payload = replace(payload, \'"property":"visibility","old":1\', \'"property":"visibility","old":"public"\')');
        $this->addSql('UPDATE koi_log l SET payload = replace(payload, \'"property":"visibility","old":2\', \'"property":"visibility","old":"private"\')');
        $this->addSql('UPDATE koi_log l SET payload = replace(payload, \'"property":"visibility","old":"public","new":1\', \'"property":"visibility","old":"public","new":"public"\')');
        $this->addSql('UPDATE koi_log l SET payload = replace(payload, \'"property":"visibility","old":"public","new":2\', \'"property":"visibility","old":"public","new":"private"\')');
        $this->addSql('UPDATE koi_log l SET payload = replace(payload, \'"property":"visibility","old":"private","new":1\', \'"property":"visibility","old":"private","new":"public"\')');
        $this->addSql('UPDATE koi_log l SET payload = replace(payload, \'"property":"visibility","old":"private","new":2\', \'"property":"visibility","old":"private","new":"private"\')');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
