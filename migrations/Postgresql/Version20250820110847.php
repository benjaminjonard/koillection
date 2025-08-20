<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250820110847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE koi_import ADD mapping JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_import ADD total_number_of_items INT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_import ADD number_of_imported_items INT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_import ADD number_of_duplicated_items INT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_import ADD number_of_skipped_items INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE koi_import DROP mapping');
        $this->addSql('ALTER TABLE koi_import DROP total_number_of_items');
        $this->addSql('ALTER TABLE koi_import DROP number_of_imported_items');
        $this->addSql('ALTER TABLE koi_import DROP number_of_duplicated_items');
        $this->addSql('ALTER TABLE koi_import DROP number_of_skipped_items');
    }
}
