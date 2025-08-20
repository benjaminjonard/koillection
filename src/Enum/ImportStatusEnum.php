<?php

declare(strict_types=1);

namespace App\Enum;

class ImportStatusEnum
{
    public const string NEW = 'new';

    public const array STATUSES = [
        self::NEW,
    ];

    public static function getStatusLabels(): array
    {
        return [
            self::NEW => 'global.import.status.new'
        ];
    }
}
