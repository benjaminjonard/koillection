<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220729144523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE koi_item ADD image_extra_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EBAA302A9657517 ON koi_item (image_extra_small_thumbnail)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_3EBAA302A9657517');
        $this->addSql('ALTER TABLE koi_item DROP image_extra_small_thumbnail');
    }
}
