<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\DisplayModeEnum;
use Doctrine\Common\Collections\Order;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20250722221709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `display_configuration_id` column on `koi_search`.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof AbstractMySQLPlatform, 'Mysql or Mariadb migration only. Skipped.');

        $this->addSql('ALTER TABLE koi_search ADD display_configuration_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_search ADD CONSTRAINT FK_565C814E165132CC FOREIGN KEY (display_configuration_id) REFERENCES koi_display_configuration (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_565C814E165132CC ON koi_search (display_configuration_id)');

        $sortingDirection = Order::Ascending->value;
        $displayMode = DisplayModeEnum::DISPLAY_MODE_GRID;
        $searches = $this->connection->createQueryBuilder()->select('id, owner_id')->from('koi_search')->executeQuery()->fetchAllAssociative();
        foreach ($searches as $search) {
            $id = Uuid::v7()->toRfc4122();
            $searchId = $search['id'];
            $ownerId = $search['owner_id'];

            $this->addSql("INSERT INTO koi_display_configuration (id, owner_id, display_mode, sorting_direction, created_at) VALUES ('$id', '$ownerId', '$displayMode', '$sortingDirection', NOW())");
            $this->addSql("UPDATE koi_search SET display_configuration_id = '$id' WHERE id = '$searchId'");
        }
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
