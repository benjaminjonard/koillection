<?php

declare(strict_types=1);

namespace App\Service\Graph;

use App\Entity\Item;
use App\Entity\User;
use App\Enum\LogTypeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChartBuilder
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * ChartBuilder constructor.
     * @param EntityManagerInterface $em
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * Build an array hour->number of item created.
     *
     * @param User $user
     * @return array
     */
    public function buildActivityByHour(User $user) : array
    {
        $sql = 'SELECT created_at as date';
        $sql .= ' FROM koi_item';
        $sql .= ' WHERE owner_id = ?';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date', 'datetime');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());
        $result = $query->getArrayResult();

        $data = [];
        //Init an array with zeroed counts for each of the 24 hours of a day
        for ($i = 0; $i < 24; ++$i) {
            $data[$i] = ['abscissa' => $i, 'count' => 0];
        }

        //Fill our array with the result of the SQL query
        $timezone = new \DateTimeZone($user->getTimezone());
        foreach ($result as $raw) {
            $hour = $raw['date']->setTimezone($timezone)->format('G');
            $data[$hour]['count']++;
        }

        return $data;
    }

    /**
     * Build an array day of the month->number of item created.
     *
     * @param User $user
     * @return array
     */
    public function buildActivityByMonthDay(User $user) : array
    {
        $sql = 'SELECT created_at AS date';
        $sql .= ' FROM koi_item';
        $sql .= ' WHERE owner_id = ?';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date', 'datetime');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());
        $result = $query->getArrayResult();

        $data = [];
        //Init an array with zeroed counts for each of the 31 possible number of days in a month
        for ($i = 1; $i <= 31; ++$i) {
            $data[] = ['abscissa' => $i, 'count' => 0];
        }

        //Fill our array with the result of the SQL query
        $timezone = new \DateTimeZone($user->getTimezone());
        foreach ($result as $raw) {
            $day = $raw['date']->setTimezone($timezone)->format('j');
            $data[$day - 1]['count']++;
        }

        return $data;
    }

    /**
     * Build an array day of the week->number of item created.
     *
     * @param User $user
     * @return array
     */
    public function buildActivityByWeekDay(User $user) : array
    {
        $sql = 'SELECT created_at AS date';
        $sql .= ' FROM koi_item';
        $sql .= ' WHERE owner_id = ?';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date', 'datetime');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());
        $result = $query->getArrayResult();

        $days = [
            mb_substr($this->translator->trans('global.days.sunday'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.days.monday'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.days.tuesday'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.days.wednesday'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.days.thursday'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.days.friday'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.days.saturday'), 0, 3, 'UTF-8')
        ];

        $data = [];
        foreach ($days as $day) {
            $data[] = ['abscissa' => $day, 'count' => 0];
        }

        //Fill our array with the result of the SQL query
        $timezone = new \DateTimeZone($user->getTimezone());
        foreach ($result as $raw) {
            $weekDay = $raw['date']->setTimezone($timezone)->format('w');
            $data[$weekDay]['count']++;
        }

        return $data;
    }

    /**
     * Build an array day of the week->number of item created.
     *
     * @param User $user
     * @return array
     */
    public function buildActivityByMonth(User $user) : array
    {
        $sql = 'SELECT created_at as date';
        $sql .= ' FROM koi_item';
        $sql .= ' WHERE owner_id = ?';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date', 'datetime');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());
        $result = $query->getArrayResult();

        $months = [
            mb_substr($this->translator->trans('global.months.january'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.february'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.march'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.april'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.may'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.june'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.july'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.august'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.september'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.october'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.november'), 0, 3, 'UTF-8'),
            mb_substr($this->translator->trans('global.months.december'), 0, 3, 'UTF-8'),
        ];

        $data = [];
        foreach ($months as $month) {
            $data[] = ['abscissa' => $month, 'count' => 0];
        }

        //Fill our array with the result of the SQL query
        $timezone = new \DateTimeZone($user->getTimezone());
        foreach ($result as $raw) {
            $month = $raw['date']->setTimezone($timezone)->format('n');
            $data[$month - 1]['count']++;
        }

        return $data;
    }

    /**
     * Build an array date->number of items at that date.
     *
     * @param User $user
     * @return array
     */
    public function buildItemEvolution(User $user) : array
    {
        $data = [];
        $sql = 'SELECT logged_at AS date, type';
        $sql .= ' FROM koi_log';
        $sql .= ' WHERE owner_id = ?';
        $sql .= ' AND object_class = ?';
        $sql .= ' AND type IN (?)';
        $sql .= ' ORDER BY date';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date', 'datetime');
        $rsm->addScalarResult('type', 'type', 'string');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());
        $query->setParameter(2, Item::class);
        $query->setParameter(3, [LogTypeEnum::TYPE_CREATE, LogTypeEnum::TYPE_DELETE]);
        $result = $query->getArrayResult();
        dump($result);die;

        $timezone = new \DateTimeZone($user->getTimezone());
        $total = 0;
        foreach ($result as $row) {
            $date = $row['date']->setTimezone($timezone);
            $timestamp = (string) $date->format($user->getDateFormat());
            $row['type'] === LogTypeEnum::TYPE_CREATE ? $total++ : $total--;
            $data[$timestamp] = $total;
        }

        return $data;
    }
}
