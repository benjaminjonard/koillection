<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220717140437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add `items_default_template_id` to `koi_collection`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('ALTER TABLE koi_collection ADD items_default_template_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B057786355C0 FOREIGN KEY (items_default_template_id) REFERENCES koi_template (id)');
        $this->addSql('CREATE INDEX IDX_7AA7B057786355C0 ON koi_collection (items_default_template_id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
