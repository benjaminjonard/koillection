<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200418215133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Rename `user_id` to `owner_id` in `koi_log` table';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_log DROP CONSTRAINT fk_9a4dc1f1a76ed395');
        $this->addSql('DROP INDEX idx_9a4dc1f1a76ed395');
        $this->addSql('ALTER TABLE koi_log RENAME COLUMN user_id TO owner_id');
        $this->addSql('ALTER TABLE koi_log ADD CONSTRAINT FK_9A4DC1F17E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9A4DC1F17E3C61F9 ON koi_log (owner_id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
