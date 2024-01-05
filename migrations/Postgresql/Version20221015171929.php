<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221015171929 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Remove default value for `cached_values`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_album ALTER cached_values DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_collection ALTER cached_values DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_wishlist ALTER cached_values DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
