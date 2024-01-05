<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230613175143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Properly set `items_default_template_id` to NULL on template deletion';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_collection DROP CONSTRAINT FK_7AA7B057786355C0');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B057786355C0 FOREIGN KEY (items_default_template_id) REFERENCES koi_template (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
