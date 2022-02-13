<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220208094033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add owner property on `koi_field`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_field ADD owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_field ADD CONSTRAINT FK_4FD5B8917E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4FD5B8917E3C61F9 ON koi_field (owner_id)');
        $this->addSql('UPDATE koi_field SET owner_id = koi_template.owner_id FROM koi_template WHERE template_id = koi_template.id');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
