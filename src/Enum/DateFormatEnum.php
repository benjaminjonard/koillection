<?php

declare(strict_types=1);

namespace App\Enum;

class DateFormatEnum
{
    public const FORMAT_SLASH_DMY = 'd/m/Y';
    public const FORMAT_SLASH_MDY = 'm/d/Y';
    public const FORMAT_SLASH_YMD = 'Y/m/d';

    public const FORMAT_HYPHEN_DMY = 'd-m-Y';
    public const FORMAT_HYPHEN_MDY = 'm-d-Y';
    public const FORMAT_HYPHEN_YMD = 'Y-m-d';

    public const CONTEXT_FORM = 'form';
    public const CONTEXT_TWIG = 'twig';
    public const CONTEXT_JS = 'js';

    public const FORMATS = [
        self::FORMAT_SLASH_DMY,
        self::FORMAT_SLASH_MDY,
        self::FORMAT_SLASH_YMD,
        self::FORMAT_HYPHEN_DMY,
        self::FORMAT_HYPHEN_MDY,
        self::FORMAT_HYPHEN_YMD,
    ];

    public const MAPPING = [
        self::FORMAT_SLASH_DMY => [self::CONTEXT_FORM => 'dd/MM/yyyy', self::CONTEXT_TWIG => 'd/m/Y', self::CONTEXT_JS => 'dd/mm/yyyy'],
        self::FORMAT_SLASH_MDY => [self::CONTEXT_FORM => 'MM/dd/yyyy', self::CONTEXT_TWIG => 'm/d/Y', self::CONTEXT_JS => 'mm/dd/yyyy'],
        self::FORMAT_SLASH_YMD => [self::CONTEXT_FORM => 'yyyy/MM/dd', self::CONTEXT_TWIG => 'Y/m/d', self::CONTEXT_JS => 'yyyy/mm/dd'],

        self::FORMAT_HYPHEN_DMY => [self::CONTEXT_FORM => 'dd-MM-yyyy', self::CONTEXT_TWIG => 'd-m-Y', self::CONTEXT_JS => 'dd-mm-yyyy'],
        self::FORMAT_HYPHEN_MDY => [self::CONTEXT_FORM => 'MM-dd-yyyy', self::CONTEXT_TWIG => 'm-d-Y', self::CONTEXT_JS => 'mm-dd-yyyy'],
        self::FORMAT_HYPHEN_YMD => [self::CONTEXT_FORM => 'yyyy-MM-dd', self::CONTEXT_TWIG => 'Y-m-d', self::CONTEXT_JS => 'yyyy-mm-dd'],
    ];

    public static function getChoicesList(): array
    {
        return [
            'global.optgroup.date_formats.slash' => [
                'global.date_formats.slash_dmy' => self::FORMAT_SLASH_DMY,
                'global.date_formats.slash_mdy' => self::FORMAT_SLASH_MDY,
                'global.date_formats.slash_ymd' => self::FORMAT_SLASH_YMD,
            ],
            'global.optgroup.date_formats.hyphen' => [
                'global.date_formats.hyphen_dmy' => self::FORMAT_HYPHEN_DMY,
                'global.date_formats.hyphen_mdy' => self::FORMAT_HYPHEN_MDY,
                'global.date_formats.hyphen_ymd' => self::FORMAT_HYPHEN_YMD,
            ],
        ];
    }

    public static function getValidationRegex(string $format): string
    {
        $separator = match ($format) {
            self::FORMAT_SLASH_DMY, self::FORMAT_SLASH_MDY, self::FORMAT_SLASH_YMD => '\/',
            self::FORMAT_HYPHEN_DMY, self::FORMAT_HYPHEN_MDY, self::FORMAT_HYPHEN_YMD => '-',
        };

        $dayRegex = "(?:(?:31($separator)(?:0?[13578]|1[02]))\\1|(?:(?:29|30)($separator)(?:0?[13-9]|1[0-2])\\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})";
        $monthRegex = "(?:29($separator)0?2\\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))";
        $yearRegex = "(?:0?[1-9]|1\d|2[0-8])($separator)(?:(?:0?[1-9])|(?:1[0-2]))\\4(?:(?:1[6-9]|[2-9]\d)?\d{2})";

        return match ($format) {
            self::FORMAT_SLASH_DMY, self::FORMAT_HYPHEN_DMY => "^$dayRegex$|^$monthRegex$|^$yearRegex$",
            self::FORMAT_SLASH_MDY, self::FORMAT_HYPHEN_MDY => "^$monthRegex$|^$dayRegex$|^$yearRegex$",
            self::FORMAT_SLASH_YMD, self::FORMAT_HYPHEN_YMD => "^$yearRegex$|^$monthRegex$|^$dayRegex$",
        };
    }
}
