<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\ConfigurationEnum;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20230525100104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add custom-light-theme-css and custom-dark-theme-css to `koi_configuration`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('ALTER TABLE koi_configuration CHANGE value value LONGTEXT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_576E96A6EA750E8 ON koi_configuration (label)');

        $id = Uuid::v7()->toRfc4122();
        $label = ConfigurationEnum::CUSTOM_LIGHT_THEME_CSS;
        $this->addSql("INSERT INTO koi_configuration (id, label, created_at) VALUES ('$id', '$label', NOW())");

        $id = Uuid::v7()->toRfc4122();
        $label = ConfigurationEnum::CUSTOM_DARK_THEME_CSS;
        $this->addSql("INSERT INTO koi_configuration (id, label, created_at) VALUES ('$id', '$label', NOW())");
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
