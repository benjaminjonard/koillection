<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\DisplayModeEnum;
use App\Enum\SortingDirectionEnum;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20220920150306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add display_configurations for `koi_user`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('ALTER TABLE koi_user ADD collections_display_configuration_id CHAR(36) DEFAULT NULL, ADD wishlists_display_configuration_id CHAR(36) DEFAULT NULL, ADD albums_display_configuration_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_user ADD CONSTRAINT FK_AC3250557C312570 FOREIGN KEY (collections_display_configuration_id) REFERENCES koi_display_configuration (id)');
        $this->addSql('ALTER TABLE koi_user ADD CONSTRAINT FK_AC325055D85B8037 FOREIGN KEY (wishlists_display_configuration_id) REFERENCES koi_display_configuration (id)');
        $this->addSql('ALTER TABLE koi_user ADD CONSTRAINT FK_AC325055ACBC164 FOREIGN KEY (albums_display_configuration_id) REFERENCES koi_display_configuration (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC3250557C312570 ON koi_user (collections_display_configuration_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC325055D85B8037 ON koi_user (wishlists_display_configuration_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC325055ACBC164 ON koi_user (albums_display_configuration_id)');

        $users = $this->connection->createQueryBuilder()->select('id')->from('koi_user')->executeQuery()->fetchAllAssociative();
        foreach ($users as $user) {
            $id = Uuid::v4()->toRfc4122();
            $userId = $user['id'];
            $displayMode = "'".DisplayModeEnum::DISPLAY_MODE_GRID."'";
            $sortingDirection = "'".SortingDirectionEnum::ASCENDING."'";

            $this->addSql("INSERT INTO koi_display_configuration (id, owner_id, display_mode, sorting_direction, created_at) VALUES ('$id', '$userId', $displayMode, $sortingDirection, NOW())");
            $this->addSql("UPDATE koi_user SET collections_display_configuration_id = '$id' WHERE id = '$userId'");

            $id = Uuid::v4()->toRfc4122();
            $this->addSql("INSERT INTO koi_display_configuration (id, owner_id, display_mode, sorting_direction, created_at) VALUES ('$id', '$userId', $displayMode, $sortingDirection, NOW())");
            $this->addSql("UPDATE koi_user SET albums_display_configuration_id = '$id' WHERE id = '$userId'");

            $id = Uuid::v4()->toRfc4122();
            $this->addSql("INSERT INTO koi_display_configuration (id, owner_id, display_mode, sorting_direction, created_at) VALUES ('$id', '$userId', $displayMode, $sortingDirection, NOW())");
            $this->addSql("UPDATE koi_user SET wishlists_display_configuration_id = '$id' WHERE id = '$userId'");
        }
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
