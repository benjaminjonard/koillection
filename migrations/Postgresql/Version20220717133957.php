<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220717133957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add `items_default_template_id` to `koi_collection`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_collection ADD items_default_template_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD CONSTRAINT FK_7AA7B057786355C0 FOREIGN KEY (items_default_template_id) REFERENCES koi_template (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7AA7B057786355C0 ON koi_collection (items_default_template_id)');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
