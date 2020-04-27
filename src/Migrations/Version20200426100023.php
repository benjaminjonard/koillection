<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200426100023 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_98E338D2C53D045F ON koi_wishlist (image)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC3250551677722F ON koi_user (avatar)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F670F2D5C53D045F ON koi_wish (image)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F670F2D59F979BF1 ON koi_wish (image_small_thumbnail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F670F2D57C533EFD ON koi_wish (image_medium_thumbnail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9779D1C53D045F ON koi_photo (image)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9779D19F979BF1 ON koi_photo (image_small_thumbnail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EBAA302C53D045F ON koi_item (image)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EBAA3029F979BF1 ON koi_item (image_small_thumbnail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EBAA3027C533EFD ON koi_item (image_medium_thumbnail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7AA7B057C53D045F ON koi_collection (image)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2DB8938AC53D045F ON koi_album (image)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16FB1EB7C53D045F ON koi_tag (image)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_16FB1EB79F979BF1 ON koi_tag (image_small_thumbnail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F991BE5C53D045F ON koi_datum (image)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F991BE59F979BF1 ON koi_datum (image_small_thumbnail)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F991BE57C533EFD ON koi_datum (image_medium_thumbnail)');
    }

    public function down(Schema $schema) : void
    {
        $this->abortIf(true, 'Always move forward.');
    }
}
