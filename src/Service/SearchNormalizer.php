<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\String\UnicodeString;

use function Symfony\Component\String\u;

class SearchNormalizer
{
    private static ?\Transliterator $kanaTransliterator = null;

    public static function normalize(string $string): string
    {
        self::$kanaTransliterator ??= \Transliterator::create('Hiragana-Katakana');

        return u(self::$kanaTransliterator->transliterate($string))
            ->folded()
            ->normalize(UnicodeString::NFD)
            ->replaceMatches('/\p{Mn}++/u', '')
            ->normalize(UnicodeString::NFC)
            ->toString()
        ;
    }

    public static function normalizeList(string $json): string
    {
        $values = json_decode($json, true);

        if (!\is_array($values)) {
            return self::normalize($json);
        }

        return json_encode(array_map(static function ($value) {
            return \is_string($value) ? self::normalize($value) : $value;
        }, $values), \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }

    public static function normalizeListElement(string $value): string
    {
        return json_encode(self::normalize($value), \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }
}
