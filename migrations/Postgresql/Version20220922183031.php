<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220922183031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add cached_values for `koi_album`, `koi_collection` and `koi_wishlist`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_album ADD cached_values JSON DEFAULT \'{}\' NOT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD cached_values JSON DEFAULT \'{}\' NOT NULL');
        $this->addSql('ALTER TABLE koi_wishlist ADD cached_values JSON DEFAULT \'{}\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
