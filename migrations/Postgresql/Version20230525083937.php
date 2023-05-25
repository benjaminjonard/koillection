<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230525083937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `custom_light_theme_css` and `custom_dark_theme_css` to `koi_user`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_user ADD custom_light_theme_css TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_user ADD custom_dark_theme_css TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
