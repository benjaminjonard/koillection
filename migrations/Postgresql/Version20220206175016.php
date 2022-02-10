<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220206175016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Update loan and datum columns';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE koi_datum ALTER type TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE koi_loan ALTER lent_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_loan ALTER lent_at DROP DEFAULT');
        $this->addSql('ALTER TABLE koi_loan ALTER returned_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE koi_loan ALTER returned_at DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
