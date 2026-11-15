<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\ConfigurationEnum;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20261115202943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add app_name and app_logo to `koi_configuration`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform, 'Mysql or Mariadb migration only. Skipped.');

        foreach ([ConfigurationEnum::APP_NAME, ConfigurationEnum::APP_LOGO] as $label) {
            $id = Uuid::v7()->toRfc4122();
            $this->addSql("INSERT INTO koi_configuration (id, label, created_at) VALUES ('$id', '$label', NOW())");
        }
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
