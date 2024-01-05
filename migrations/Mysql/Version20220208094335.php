<?php

declare(strict_types=1);

namespace App\Migrations\Mysql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220208094335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Mysql] Add owner property on `koi_field`';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof MySQLPlatform, 'Migration can only be executed safely on \'mysql\' or \'mariadb\'.');

        $this->addSql('ALTER TABLE koi_field ADD owner_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_field ADD CONSTRAINT FK_4FD5B8917E3C61F9 FOREIGN KEY (owner_id) REFERENCES koi_user (id)');
        $this->addSql('CREATE INDEX IDX_4FD5B8917E3C61F9 ON koi_field (owner_id)');
        $this->addSql('UPDATE koi_field LEFT JOIN koi_template ON koi_field.template_id = koi_template.id SET koi_field.owner_id = koi_template.owner_id');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
