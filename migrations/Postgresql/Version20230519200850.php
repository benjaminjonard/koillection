<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\ConfigurationEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20230519200850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `koi_configuration` table';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE koi_configuration (id CHAR(36) NOT NULL, label VARCHAR(255) NOT NULL, value VARCHAR(255), created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN koi_configuration.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN koi_configuration.updated_at IS \'(DC2Type:datetime_immutable)\'');

        $id = Uuid::v4()->toRfc4122();
        $label = ConfigurationEnum::THUMBNAILS_FORMAT;
        $this->addSql("INSERT INTO koi_configuration (id, label, created_at) VALUES ('$id', '$label', NOW())");
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
