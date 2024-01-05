<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230613175623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Properly set `items_default_template_id` to NULL on template deletion';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('ALTER TABLE koi_collection DROP FOREIGN KEY FK_7AA7B057786355C0');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B057786355C0 FOREIGN KEY (items_default_template_id) REFERENCES koi_template (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
