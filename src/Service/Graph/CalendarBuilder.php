<?php

declare(strict_types=1);

namespace App\Service\Graph;

use App\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

class CalendarBuilder
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    public function buildItemCalendar(User $user): array
    {
        $data = [];
        $sql = 'SELECT created_at AS date';
        $sql .= ' FROM koi_item';
        $sql .= ' WHERE owner_id = ?';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date', 'datetime');

        $query = $this->managerRegistry->getManager()->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());

        $result = $query->getArrayResult();

        $timezone = new \DateTimeZone($user->getTimezone());
        foreach ($result as $row) {
            $date = $row['date']->setTimezone($timezone);
            $year = (string) $date->format('Y');
            $timestamp = (string) $date->format('Y-m-d');
            isset($data[$year]) ?: $data[$year] = [];
            isset($data[$year][$timestamp]) ?: $data[$year][$timestamp] = [$timestamp, 0];
            ++$data[$year][$timestamp][1];
        }

        return $data;
    }
}
