<?php

declare(strict_types=1);

namespace App\Enum;

class ChoiceListTypeEnum
{
    public const TYPE_SELECT = 'select';
    public const TYPE_RADIO_BUTTONS = 'radio-buttons';
    public const TYPE_CHECKBOXES = 'checkboxes';

    public const TYPES = [
        self::TYPE_SELECT,
        self::TYPE_RADIO_BUTTONS,
        self::TYPE_CHECKBOXES,
    ];

    public const TYPES_TRANS_KEYS = [
        self::TYPE_SELECT => 'label.select',
        self::TYPE_RADIO_BUTTONS => 'label.radio_buttons',
        self::TYPE_CHECKBOXES => 'label.checkboxes',
    ];

    public static function getTypesLabels(): array
    {
        return self::TYPES_TRANS_KEYS;
    }

    public static function getTypeLabel(string $type): string
    {
        return self::TYPES_TRANS_KEYS[$type];
    }
}
