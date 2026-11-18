<?php

declare(strict_types=1);

namespace App\Enum;

class ImportStatusEnum
{
    public const string PENDING = 'pending';

    public const string PROCESSING = 'processing';

    public const string DONE = 'done';

    public const string FAILED = 'failed';
}
