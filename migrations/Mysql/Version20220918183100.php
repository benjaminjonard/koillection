<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\SortingDirectionEnum;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Uuid;

final class Version20220918183100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add display_configuration for `koi_tag`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('ALTER TABLE koi_tag ADD items_display_configuration_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_tag ADD CONSTRAINT FK_16FB1EB7A4A07C77 FOREIGN KEY (items_display_configuration_id) REFERENCES koi_display_configuration (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16FB1EB7A4A07C77 ON koi_tag (items_display_configuration_id)');

        $tags = $this->connection->createQueryBuilder()->select('id, owner_id, items_display_mode')->from('koi_tag')->executeQuery()->fetchAllAssociative();
        foreach ($tags as $tag) {
            $id = Uuid::v4()->toRfc4122();
            $tagId = $tag['id'];
            $ownerId = $tag['owner_id'];
            $displayMode = !empty($tag['items_display_mode']) ? "'" . $tag['items_display_mode'] . "'" : 'NULL';
            $sortingDirection = "'" . SortingDirectionEnum::ASCENDING . "'";

            $this->addSql("INSERT INTO koi_display_configuration (id, owner_id, display_mode, sorting_direction, created_at) VALUES ('$id', '$ownerId', $displayMode, $sortingDirection, NOW())");
            $this->addSql("UPDATE koi_tag SET items_display_configuration_id = '$id' WHERE id = '$tagId'");
        }

        $this->addSql('ALTER TABLE koi_tag DROP items_display_mode');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
