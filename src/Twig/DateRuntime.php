<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class DateRuntime implements RuntimeExtensionInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * DateExtension constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param \DateTime $ago
     * @return string
     * @throws \Exception
     */
    public function timeAgo(\DateTime $ago) : string
    {
        $now = new \DateTime();
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $this->translator->trans("global.time.$v", ['%count%' => $diff->$k]);
            } else {
                unset($string[$k]);
            }
        }
        $string = \array_slice($string, 0, 1);

        return $string ?
            $this->translator->trans('global.time.ago', ['%time%' => implode(', ', $string)]) : $this->translator->trans('global.time.just_now');
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @return string
     */
    public function timeDiff(\DateTime $start, \DateTime $end) : string
    {
        $diff = $start->diff($end);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        ];
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $this->translator->trans("global.time.$v", ['%count%' => $diff->$k]);
            } else {
                unset($string[$k]);
            }
        }
        $string = \array_slice($string, 0, 1);

        return $string ? implode(', ', $string) : '';
    }

    /**
     * @param \DateTime $ago
     * @return string
     */
    public function dateAgo(\DateTime $ago) : string
    {
        $now = new \DateTime();
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = [
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day'
        ];

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $this->translator->trans("global.time.$v", ['%count%' => $diff->$k]);
            } else {
                unset($string[$k]);
            }
        }
        $string = \array_slice($string, 0, 1);

        return $string ?
            $this->translator->trans('global.time.ago', ['%time%' => implode(', ', $string)]) : $this->translator->trans('global.time.today');
    }
}