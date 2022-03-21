<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200417113047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `parent` and `image` propertise to table `koi_album`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_album ADD image_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_album ADD parent_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN koi_album.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN koi_album.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE koi_album ADD CONSTRAINT FK_2DB8938A3DA5256D FOREIGN KEY (image_id) REFERENCES koi_image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE koi_album ADD CONSTRAINT FK_2DB8938A727ACA70 FOREIGN KEY (parent_id) REFERENCES koi_album (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DB8938A3DA5256D ON koi_album (image_id)');
        $this->addSql('CREATE INDEX IDX_2DB8938A727ACA70 ON koi_album (parent_id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
