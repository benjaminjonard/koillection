<?php

declare(strict_types=1);

namespace App\Enum;

class ImportStatusEnum
{
    public const string NEW = 'new';

    public const string DONE = 'done';

    public const array STATUSES = [
        self::NEW,
        self::DONE,
    ];

    public static function getStatusLabels(): array
    {
        return [
            self::NEW => 'global.import.status.new',
            self::DONE => 'global.import.status.done',
        ];
    }
}
