<?php

declare(strict_types=1);

namespace App\Enum;

class DisplayModeEnum
{
    public const DISPLAY_MODE_GRID = 'grid';
    public const DISPLAY_MODE_LIST = 'list';

    public const DISPLAY_MODES = [
        self::DISPLAY_MODE_GRID,
        self::DISPLAY_MODE_LIST,
    ];

    public static function getDisplayModeLabels(): array
    {
        return [
            self::DISPLAY_MODE_GRID => 'global.display_mode.grid',
            self::DISPLAY_MODE_LIST => 'global.display_mode.list',
        ];
    }
}
