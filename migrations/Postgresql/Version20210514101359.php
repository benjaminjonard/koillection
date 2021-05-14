<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210514101359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('UPDATE koi_album SET visibility = \'internal\' WHERE visibility = \'authenticated-users-only\';');
        $this->addSql('UPDATE koi_collection SET visibility = \'internal\' WHERE visibility = \'authenticated-users-only\';');
        $this->addSql('UPDATE koi_datum SET visibility = \'internal\' WHERE visibility = \'authenticated-users-only\';');
        $this->addSql('UPDATE koi_item SET visibility = \'internal\' WHERE visibility = \'authenticated-users-only\';');
        $this->addSql('UPDATE koi_photo SET visibility = \'internal\' WHERE visibility = \'authenticated-users-only\';');
        $this->addSql('UPDATE koi_tag SET visibility = \'internal\' WHERE visibility = \'authenticated-users-only\';');
        $this->addSql('UPDATE koi_user SET visibility = \'internal\' WHERE visibility = \'authenticated-users-only\';');
        $this->addSql('UPDATE koi_wish SET visibility = \'internal\' WHERE visibility = \'authenticated-users-only\';');
        $this->addSql('UPDATE koi_wishlist SET visibility = \'internal\' WHERE visibility = \'authenticated-users-only\';');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
