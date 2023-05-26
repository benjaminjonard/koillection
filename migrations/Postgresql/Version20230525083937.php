<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\ConfigurationEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20230525083937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add custom-light-theme-css and custom-dark-theme-css to `koi_configuration`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_configuration ALTER value TYPE TEXT');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_576E96A6EA750E8 ON koi_configuration (label)');

        $id = Uuid::v4()->toRfc4122();
        $label = ConfigurationEnum::CUSTOM_LIGHT_THEME_CSS;
        $this->addSql("INSERT INTO koi_configuration (id, label, created_at) VALUES ('$id', '$label', NOW())");

        $id = Uuid::v4()->toRfc4122();
        $label = ConfigurationEnum::CUSTOM_DARK_THEME_CSS;
        $this->addSql("INSERT INTO koi_configuration (id, label, created_at) VALUES ('$id', '$label', NOW())");
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
