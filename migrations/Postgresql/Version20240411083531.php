<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\ConfigurationEnum;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20240411083531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add enable_metrics to `koi_configuration`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $id = Uuid::v7()->toRfc4122();
        $label = ConfigurationEnum::ENABLE_METRICS;
        $this->addSql("INSERT INTO koi_configuration (id, label, created_at) VALUES ('$id', '$label', NOW())");
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
