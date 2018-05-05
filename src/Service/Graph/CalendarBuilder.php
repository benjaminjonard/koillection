<?php

namespace App\Service\Graph;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Class CalendarBuilder
 *
 * @package App\Service\Graph
 */
class CalendarBuilder
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * CalendarBuilder constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Build an array date->number of item created.
     *
     * @param User $user
     * @return array
     */
    public function buildItemCalendar(User $user) : array
    {
        $data = [];
        $result = $this->getNumberOfCreatedContentsByDate($user);

        foreach ($result as $row) {
            $timestamp = strtotime($row['date']);
            $details = getdate((int) $timestamp);
            $data[$details['year']]["$timestamp"] = (int) $row['count'];
        }

        return $data;
    }

    /**
     * @param User $user
     * @return array
     */
    private function getNumberOfCreatedContentsByDate(User $user) : array
    {
        $sql = "SELECT to_char(created_at, 'YYYY-mm-dd') AS date, COUNT(id) as count";
        $sql .= ' FROM koi_item';
        $sql .= ' WHERE owner_id = ? GROUP BY date ORDER BY date';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date');
        $rsm->addScalarResult('count', 'count');

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter(1, $user->getId());

        return $query->getArrayResult();
    }
}
