<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230801125257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `scraped_from_url` to `koi_item` and `koi_collection`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD scraped_from_url TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_item ADD scraped_from_url TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
