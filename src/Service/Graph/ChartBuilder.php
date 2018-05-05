<?php

namespace App\Service\Graph;

use App\Entity\User;
use App\Enum\LocaleEnum;
use App\Enum\ThemeEnum;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ChartBuilder
 *
 * @package App\Service\Graph
 */
class ChartBuilder
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

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
        $sql = "SELECT to_char(created_at, 'fmHH24') AS abscissa, COUNT(id) as count";
        $sql .= ' FROM koi_item';
        $sql .= ' WHERE owner_id = ? GROUP BY abscissa';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('abscissa', 'abscissa');
        $rsm->addScalarResult('count', 'count');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());
        $result = $query->getArrayResult();

        $data = [];
        //Init an array with zeroed counts for each of the 24 hours of a day
        for ($i = 0; $i < 24; ++$i) {
            $data[$i] = ['abscissa' => $i, 'count' => 0];
        }
        //Fill our array with the result of the SQL query
        foreach ($result as $raw) {
            $data[$raw['abscissa']] = ['abscissa' => (int) $raw['abscissa'], 'count' => (int) $raw['count']];
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
        $sql = "SELECT to_char(created_at, 'fmDD') AS abscissa, COUNT(id) as count";
        $sql .= ' FROM koi_item';
        $sql .= ' WHERE owner_id = ? GROUP BY abscissa';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('abscissa', 'abscissa');
        $rsm->addScalarResult('count', 'count');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());
        $result = $query->getArrayResult();

        $data = [];
        //Init an array with zeroed counts for each of the 31 possible number of days in a month
        //Start array to 0, otherwise javascript is not happy when parsing json :(
        for ($i = 0; $i <= 31; ++$i) {
            $data[$i] = ['abscissa' => $i, 'count' => 0];
        }
        //Fill our array with the result of the SQL query
        foreach ($result as $raw) {
            $data[$raw['abscissa'] - 1] = ['abscissa' => (int) $raw['abscissa'], 'count' => (int) $raw['count']];
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
        $sql = "SELECT to_char(created_at, 'D') AS abscissa, COUNT(id) as count";
        $sql .= ' FROM koi_item';
        $sql .= ' WHERE owner_id = ? GROUP BY abscissa';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('abscissa', 'abscissa');
        $rsm->addScalarResult('count', 'count');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());
        $result = $query->getArrayResult();

        $days = [
            $this->translator->trans('global.days.sunday'),
            $this->translator->trans('global.days.monday'),
            $this->translator->trans('global.days.tuesday'),
            $this->translator->trans('global.days.wednesday'),
            $this->translator->trans('global.days.thursday'),
            $this->translator->trans('global.days.friday'),
            $this->translator->trans('global.days.saturday'),
        ];

        $data = [];
        foreach ($days as $day) {
            $data[] = ['abscissa' => $day, 'count' => 0];
        }

        //Fill our array with the result of the SQL query
        foreach ($result as $raw) {
            $data[$raw['abscissa'] - 1] = ['abscissa' => $days[$raw['abscissa'] - 1], 'count' => (int) $raw['count']];
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
        $sql = "SELECT to_char(created_at, 'fmMM') AS abscissa, COUNT(id) as count";
        $sql .= ' FROM koi_item';
        $sql .= ' WHERE owner_id = ? GROUP BY abscissa';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('abscissa', 'abscissa');
        $rsm->addScalarResult('count', 'count');

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
        foreach ($result as $raw) {
            $data[$raw['abscissa'] - 1] = ['abscissa' => $months[$raw['abscissa'] - 1], 'count' => (int) $raw['count']];
        }

        return $data;
    }

    public function buildThemesUsage()
    {
        $sql = "SELECT theme AS abscissa, COUNT(id) as count";
        $sql .= ' FROM koi_user';
        $sql .= ' GROUP BY abscissa';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('abscissa', 'abscissa');
        $rsm->addScalarResult('count', 'count');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $result = $query->getArrayResult();

        $count = $this->em->getRepository(User::class)->count([]);

        $data = [];
        foreach (ThemeEnum::THEMES as $theme) {
            $datum = ['abscissa' => $this->translator->trans("global.themes.$theme").' (0%)', 'count' => 0];
            foreach ($result as $raw) {
                if ($raw['abscissa'] === $theme) {
                    $percent = round(($raw['count'] * 100) / $count, 2);
                    $datum['abscissa'] = $this->translator->trans("global.themes.$theme");
                    $datum['count'] = $raw['count'];
                    $datum['percent'] = " ($percent%)";
                }
            }
            $data[] = $datum;
        }

        return $data;
    }

    public function buildLocalesUsage()
    {
        $sql = "SELECT locale AS abscissa, COUNT(id) as count";
        $sql .= ' FROM koi_user';
        $sql .= ' GROUP BY abscissa';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('abscissa', 'abscissa');
        $rsm->addScalarResult('count', 'count');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $result = $query->getArrayResult();
        $count = $this->em->getRepository(User::class)->count([]);

        $data = [];
        foreach (LocaleEnum::LOCALES_TRANS_KEYS as $key => $locale) {
            $datum = ['abscissa' => $this->translator->trans("global.locale.$locale").' (0%)', 'count' => 0];
            foreach ($result as $raw) {
                if ($raw['abscissa'] === $key) {
                    $percent = round(($raw['count'] * 100) / $count, 2);
                    $datum['abscissa'] = $this->translator->trans("global.locale.$locale");
                    $datum['count'] = $raw['count'];
                    $datum['percent'] = " ($percent%)";
                }
            }
            $data[] = $datum;
        }

        return $data;
    }

    /**
     * @param \DateTime|null $since
     * @param int $count
     * @param string $primary
     * @param null|string $secondary
     * @return array
     */
    public function buildAnalyticsChart(?\DateTime $since, int $count, string $primary, ?string $secondary = null)
    {
        $unknown = $this->translator->trans('global.unknown');
        if ($secondary) {
            $sql = "SELECT CONCAT(COALESCE($primary, '$unknown'), ' - ', COALESCE($secondary, '$unknown')) AS abscissa, COUNT(user_id) AS count";
        } else {
            $sql = "SELECT COALESCE($primary, '$unknown') AS abscissa, COUNT(user_id) AS count";
        }

        $sql .= ' FROM koi_connection';
        if ($since instanceof \DateTime) {
            $sql .= " WHERE date > '".$since->format("Y-m-d")."'";
        }
        $sql .= ' GROUP BY abscissa';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('abscissa', 'abscissa');
        $rsm->addScalarResult('count', 'count');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $result = $query->getArrayResult();

        $data = [];
        $others = ['percent' => 0, 'count' => 0];

        foreach ($result as $raw) {
            $percent = round(($raw['count'] * 100) / $count, 2);
            if ($percent < 1) {
                $others['percent'] += $percent;
                $others['count'] += $raw['count'];
            } else {
                $datum['abscissa'] = $raw['abscissa'];
                $datum['count'] = $raw['count'];
                $datum['percent'] = " ($percent%)";
                $data[] = $datum;
            }
        }

        if ($others['count'] > 0) {
            $percent = $others['percent'];
            $datum['abscissa'] = $this->translator->trans('global.others');
            $datum['count'] = $others['count'];
            $datum['percent'] = " ($percent%)";
            $data[] = $datum;
        }

        usort($data, function($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return $data;
    }
}
