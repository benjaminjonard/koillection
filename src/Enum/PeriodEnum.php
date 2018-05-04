<?php

namespace App\Enum;

/**
 * Class PeriodEnum
 *
 * @package App\Enum
 */
class PeriodEnum
{
    const PERIOD_TODAY = 'today';
    const PERIOD_DAY = 'day';
    const PERIOD_WEEK = 'week';
    const PERIOD_MONTH = 'month';
    const PERIOD_YEAR = 'year';
    const PERIOD_ALL = 'all';

    const PERIODS = [
        self::PERIOD_TODAY,
        self::PERIOD_DAY,
        self::PERIOD_WEEK,
        self::PERIOD_MONTH,
        self::PERIOD_YEAR,
        self::PERIOD_ALL
    ];

    /**
     * @return array
     */
    public static function getThemeLabels() : array
    {
        return self::PERIODS;
    }

    /**
     * @param $period
     * @return \DateTime|null
     */
    public static function getDateSince($period) : ?\DateTime {
        switch ($period) {
            case self::PERIOD_ALL:
                return null;
            case self::PERIOD_DAY:
                return(new \DateTime())->modify('-1 day');
            case self::PERIOD_WEEK:
                return (new \DateTime())->modify('-1 week');
            case self::PERIOD_MONTH:
                return (new \DateTime())->modify('-1 month');
            case self::PERIOD_YEAR:
                return (new \DateTime())->modify('-1 year');
            case self::PERIOD_TODAY:
            default:
                return (new \DateTime())->modify('today');
        }
    }
}
