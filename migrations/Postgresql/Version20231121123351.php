<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\ScraperTypeEnum;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231121123351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE koi_scraper ADD type VARCHAR(15)');
        $this->addSql('UPDATE koi_scraper SET type = ?', [ScraperTypeEnum::TYPE_ITEM]);
        $this->addSql('ALTER TABLE koi_scraper ALTER COLUMN type SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE koi_scraper DROP type');
    }
}
