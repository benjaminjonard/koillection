<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230801130420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `scraped_from_url` to `koi_item` and `koi_collection`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD scraped_from_url LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_item ADD scraped_from_url LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
