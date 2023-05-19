<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\ConfigurationEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20230519201736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `koi_configuration` table';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE koi_configuration (id CHAR(36) NOT NULL, label VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $id = Uuid::v4()->toRfc4122();
        $label = ConfigurationEnum::THUMBNAILS_FORMAT;
        $this->addSql("INSERT INTO koi_configuration (id, label, created_at) VALUES ('$id', '$label', NOW())");
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
