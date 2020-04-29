<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20190702133405 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add index on `visibility` for every table having this property.';
    }

    public function up(Schema $schema) : void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE INDEX idx_user_visibility ON koi_user (visibility)');
        $this->addSql('CREATE INDEX idx_album_visibility ON koi_album (visibility)');
        $this->addSql('CREATE INDEX idx_collection_visibility ON koi_collection (visibility)');
        $this->addSql('CREATE INDEX idx_item_visibility ON koi_item (visibility)');
        $this->addSql('CREATE INDEX idx_photo_visibility ON koi_photo (visibility)');
        $this->addSql('CREATE INDEX idx_tag_visibility ON koi_tag (visibility)');
        $this->addSql('CREATE INDEX idx_wish_visibility ON koi_wish (visibility)');
        $this->addSql('CREATE INDEX idx_wishlist_visibility ON koi_wishlist (visibility)');
        $this->addSql('CREATE INDEX idx_datum_visibility ON koi_datum (visibility)');
    }

    public function down(Schema $schema) : void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
