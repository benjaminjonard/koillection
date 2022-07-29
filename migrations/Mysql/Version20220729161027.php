<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220729161027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE koi_album ADD image_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DB8938A9F979BF1 ON koi_album (image_small_thumbnail)');
        $this->addSql('ALTER TABLE koi_user ADD avatar_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC325055E3F37574 ON koi_user (avatar_small_thumbnail)');
        $this->addSql('ALTER TABLE koi_wishlist ADD image_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_98E338D29F979BF1 ON koi_wishlist (image_small_thumbnail)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_2DB8938A9F979BF1');
        $this->addSql('ALTER TABLE koi_album DROP image_small_thumbnail');
        $this->addSql('DROP INDEX UNIQ_AC325055E3F37574');
        $this->addSql('ALTER TABLE koi_user DROP avatar_small_thumbnail');
        $this->addSql('DROP INDEX UNIQ_98E338D29F979BF1');
        $this->addSql('ALTER TABLE koi_wishlist DROP image_small_thumbnail');
    }
}
