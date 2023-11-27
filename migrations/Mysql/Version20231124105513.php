<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use App\Enum\ScraperTypeEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231124105513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `price_path` to `koi_scraper`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_scraper ADD type VARCHAR(15), ADD price_path LONGTEXT DEFAULT NULL');
        $this->addSql('UPDATE koi_scraper SET type = ?', [ScraperTypeEnum::TYPE_ITEM]);
        $this->addSql('ALTER TABLE koi_scraper MODIFY type VARCHAR(15) NOT NULL');

        $this->addSql('ALTER TABLE koi_wish ADD scraped_from_url LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');

    }
}
