<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210514100228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Update visibility values';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE koi_album SET visibility = "internal" WHERE visibility = "authenticated-users-only";');
        $this->addSql('UPDATE koi_collection SET visibility = "internal" WHERE visibility = "authenticated-users-only";');
        $this->addSql('UPDATE koi_datum SET visibility = "internal" WHERE visibility = "authenticated-users-only";');
        $this->addSql('UPDATE koi_item SET visibility = "internal" WHERE visibility = "authenticated-users-only";');
        $this->addSql('UPDATE koi_photo SET visibility = "internal" WHERE visibility = "authenticated-users-only";');
        $this->addSql('UPDATE koi_tag SET visibility = "internal" WHERE visibility = "authenticated-users-only";');
        $this->addSql('UPDATE koi_user SET visibility = "internal" WHERE visibility = "authenticated-users-only";');
        $this->addSql('UPDATE koi_wish SET visibility = "internal" WHERE visibility = "authenticated-users-only";');
        $this->addSql('UPDATE koi_wishlist SET visibility = "internal" WHERE visibility = "authenticated-users-only";');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
