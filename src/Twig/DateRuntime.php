<?php

declare(strict_types=1);

namespace App\Twig;

use App\Enum\DateFormatEnum;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class DateRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function timeAgo(\DateTimeImmutable $ago): string
    {
        $parts = $this->getIntervalParts(new \DateTimeImmutable(), $ago);
        $key = array_key_first($parts);

        if ($key) {
            $time = $this->translator->trans("global.time.{$key}", ['count' => $parts[$key]]);

            return $this->translator->trans('global.time.ago', ['time' => $time]);
        } else {
            return $this->translator->trans('global.time.just_now');
        }
    }

    public function timeDiff(\DateTimeImmutable $start, \DateTimeImmutable $end): string
    {
        $parts = $this->getIntervalParts($start, $end);
        $key = array_key_first($parts);

        return $this->translator->trans("global.time.{$key}", ['count' => $parts[$key]]);
    }

    private function getIntervalParts(\DateTimeImmutable $start, \DateTimeImmutable $end): array
    {
        $diff = $start->diff($end);

        $week = (int) floor($diff->d / 7);
        $day = $diff->d - $week * 7;

        return array_filter([
            'year' => $diff->y,
            'month' => $diff->m,
            'week' => $week,
            'day' => $day,
            'hour' => $diff->h,
            'minute' => $diff->m,
            'second' => $diff->s,
        ]);
    }

    public function getValidationRegexForDateFormat(string $dateFormat) : string
    {
        return DateFormatEnum::getValidationRegex($dateFormat);
    }
}
