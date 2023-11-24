<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\ScraperTypeEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231124104927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `price_path` to `koi_scraper`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_scraper ADD type VARCHAR(15)');
        $this->addSql('UPDATE koi_scraper SET type = ?', [ScraperTypeEnum::TYPE_ITEM]);
        $this->addSql('ALTER TABLE koi_scraper ALTER COLUMN type SET NOT NULL');

        $this->addSql('ALTER TABLE koi_scraper ADD price_path TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_wish ADD scraped_from_url TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
