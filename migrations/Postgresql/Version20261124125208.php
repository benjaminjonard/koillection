<?php

declare(strict_types=1);

namespace App\Migrations\Postgresql;

use App\Enum\DatumTypeEnum;
use App\Service\SearchNormalizer;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261124125208 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '[Postgresql] Add normalized columns used for accent-insensitive search.';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(!$this->connection->getDatabasePlatform() instanceof PostgreSQLPlatform, 'Postgresql migration only. Skipped.');

        $this->addSql('ALTER TABLE koi_album ADD normalized_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_collection ADD normalized_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_datum ADD normalized_value TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_item ADD normalized_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_tag ADD normalized_label VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE koi_wishlist ADD normalized_name VARCHAR(255) DEFAULT NULL');

        $columns = [
            'koi_album' => ['title', 'normalized_title'],
            'koi_collection' => ['title', 'normalized_title'],
            'koi_item' => ['name', 'normalized_name'],
            'koi_tag' => ['label', 'normalized_label'],
            'koi_wishlist' => ['name', 'normalized_name'],
        ];

        foreach ($columns as $table => [$column, $normalizedColumn]) {
            $rows = $this->connection->createQueryBuilder()->select("id, {$column}")->from($table)->executeQuery()->fetchAllAssociative();
            foreach ($rows as $row) {
                $this->addSql("UPDATE {$table} SET {$normalizedColumn} = ? WHERE id = ?", [SearchNormalizer::normalize($row[$column]), $row['id']]);
            }
        }

        $data = $this->connection->createQueryBuilder()->select('id, type, value')->from('koi_datum')->where('value IS NOT NULL')->executeQuery()->fetchAllAssociative();
        foreach ($data as $datum) {
            $value = $datum['value'];
            $normalizedValue = SearchNormalizer::normalize($value);

            if ($datum['type'] === DatumTypeEnum::TYPE_LIST || $datum['type'] === DatumTypeEnum::TYPE_CHOICE_LIST) {
                $decodedValue = json_decode($value, true);
                if (\is_array($decodedValue)) {
                    $value = json_encode($decodedValue, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
                }

                $normalizedValue = SearchNormalizer::normalizeList($value);
            }

            $this->addSql('UPDATE koi_datum SET value = ?, normalized_value = ? WHERE id = ?', [$value, $normalizedValue, $datum['id']]);
        }
    }

    public function down(Schema $schema): void
    {
        $this->skipIf(true, 'Always move forward.');
    }
}
