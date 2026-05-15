<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\ScraperContentTypeEnum;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260514194959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `content_type` column on `koi_scraper`.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Mysql or Mariadb migration only. Skipped.');

        $this->addSql('ALTER TABLE koi_scraper ADD content_type VARCHAR(15)');
        $this->addSql('UPDATE koi_scraper SET content_type = ?', [ScraperContentTypeEnum::CONTENT_TYPE_HTML]);
        $this->addSql('ALTER TABLE koi_scraper MODIFY content_type VARCHAR(15) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
