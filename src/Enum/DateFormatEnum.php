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
        self::FORMAT_SLASH_DMY,
        self::FORMAT_SLASH_YMD,
        self::FORMAT_HYPHEN_DMY,
        self::FORMAT_HYPHEN_MDY,
        self::FORMAT_HYPHEN_YMD
    ];

    public const MAPPING = [
        self::FORMAT_SLASH_DMY => [self::CONTEXT_FORM => 'dd/MM/yyyy', self::CONTEXT_TWIG => 'd/m/Y', self::CONTEXT_JS => 'dd/mm/yyyy'],
        self::FORMAT_SLASH_MDY => [self::CONTEXT_FORM => 'MM/dd/yyyy', self::CONTEXT_TWIG => 'm/d/Y', self::CONTEXT_JS => 'mm/dd/yyyy'],
        self::FORMAT_SLASH_YMD => [self::CONTEXT_FORM => 'yyyy/MM/dd', self::CONTEXT_TWIG => 'Y/m/d', self::CONTEXT_JS => 'yyyy/mm/dd'],

        self::FORMAT_HYPHEN_DMY => [self::CONTEXT_FORM => 'dd-MM-yyyy', self::CONTEXT_TWIG => 'd-m-Y', self::CONTEXT_JS => 'dd-mm-yyyy'],
        self::FORMAT_HYPHEN_MDY => [self::CONTEXT_FORM => 'MM-dd-yyyy', self::CONTEXT_TWIG => 'm-d-Y', self::CONTEXT_JS => 'mm-dd-yyyy'],
        self::FORMAT_HYPHEN_YMD => [self::CONTEXT_FORM => 'yyyy-MM-dd', self::CONTEXT_TWIG => 'Y-m-d', self::CONTEXT_JS => 'yyyy-mm-dd'],
    ];

    public static function getChoicesList() : array
    {
        return [
            'global.optgroup.date_formats.slash' => [
                'global.date_formats.slash_dmy' => self::FORMAT_SLASH_DMY,
                'global.date_formats.slash_mdy' => self::FORMAT_SLASH_MDY,
                'global.date_formats.slash_ymd' => self::FORMAT_SLASH_YMD
            ],
            'global.optgroup.date_formats.hyphen' => [
                'global.date_formats.hyphen_dmy' => self::FORMAT_HYPHEN_DMY,
                'global.date_formats.hyphen_mdy' => self::FORMAT_HYPHEN_MDY,
                'global.date_formats.hyphen_ymd' => self::FORMAT_HYPHEN_YMD
            ],
        ];
    }
}
