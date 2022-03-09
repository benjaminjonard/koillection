<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200923193955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add features booleans on `koi_user`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE koi_user ADD wishlists_feature_enabled TINYINT(1) DEFAULT \'1\' NOT NULL, ADD tags_feature_enabled TINYINT(1) DEFAULT \'1\' NOT NULL, ADD signs_feature_enabled TINYINT(1) DEFAULT \'1\' NOT NULL, ADD albums_feature_enabled TINYINT(1) DEFAULT \'1\' NOT NULL, ADD loans_feature_enabled TINYINT(1) DEFAULT \'1\' NOT NULL, ADD templates_feature_enabled TINYINT(1) DEFAULT \'1\' NOT NULL, ADD history_feature_enabled TINYINT(1) DEFAULT \'1\' NOT NULL, ADD statistics_feature_enabled TINYINT(1) DEFAULT \'1\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
