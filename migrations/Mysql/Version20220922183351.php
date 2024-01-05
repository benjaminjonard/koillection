<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220922183351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add cached_values for `koi_album`, `koi_collection` and `koi_wishlist`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('ALTER TABLE koi_album ADD cached_values JSON NOT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD cached_values JSON NOT NULL');
        $this->addSql('ALTER TABLE koi_wishlist ADD cached_values JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
