<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220730075509 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add more thumbnails sizes';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_album ADD image_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DB8938A9F979BF1 ON koi_album (image_small_thumbnail)');
        $this->addSql('ALTER TABLE koi_collection ADD image_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7AA7B0579F979BF1 ON koi_collection (image_small_thumbnail)');
        $this->addSql('ALTER TABLE koi_datum ADD image_extra_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F991BE5A9657517 ON koi_datum (image_extra_small_thumbnail)');
        $this->addSql('ALTER TABLE koi_item ADD image_extra_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EBAA302A9657517 ON koi_item (image_extra_small_thumbnail)');
        $this->addSql('ALTER TABLE koi_user ADD avatar_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC325055E3F37574 ON koi_user (avatar_small_thumbnail)');
        $this->addSql('ALTER TABLE koi_wishlist ADD image_small_thumbnail VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_98E338D29F979BF1 ON koi_wishlist (image_small_thumbnail)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
