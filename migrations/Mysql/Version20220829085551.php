<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220829085551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Update locale values';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE koi_user SET locale = \'en\' WHERE locale = \'en-GB\';');
        $this->addSql('UPDATE koi_user SET locale = \'fr\' WHERE locale = \'fr-FR\';');
        $this->addSql('UPDATE koi_user SET locale = \'es\' WHERE locale = \'es-ES\';');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
