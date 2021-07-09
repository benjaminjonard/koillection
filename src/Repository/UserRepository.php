<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Enum\DatumTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByUsernameOrEmail($login) : ?User
    {
        $user = $this
            ->createQueryBuilder('u')
            ->where('u.username = :login OR u.email = :login')
            ->setParameter('login', $login)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function getCounters(User $user) : array
    {
        $collectionsSubQuery = 'SELECT COUNT(*) FROM koi_collection WHERE owner_id = ?';
        $itemsSubQuery = 'SELECT COUNT(*) FROM koi_item WHERE owner_id = ?';
        $tagsSubQuery = 'SELECT COUNT(*) FROM koi_tag WHERE owner_id = ?';
        $signsSubQuery = 'SELECT COUNT(*) FROM koi_datum d LEFT JOIN koi_item i ON i.id = d.item_id WHERE i.owner_id = ? AND d.type = ?';

        $sql = "SELECT ($collectionsSubQuery) AS collections, ($itemsSubQuery) AS items, ($tagsSubQuery) AS tags, ($signsSubQuery) AS signs";
        $sql .= ' FROM koi_user u';
        $sql .= ' WHERE u.id = ?';

        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('collections', 'collections')
            ->addScalarResult('items', 'items')
            ->addScalarResult('tags', 'tags')
            ->addScalarResult('signs', 'signs')
        ;

        $query = $this->_em->createNativeQuery($sql, $rsm);
        $query
            ->setParameter(1, $user->getId())
            ->setParameter(2, $user->getId())
            ->setParameter(3, $user->getId())
            ->setParameter(4, $user->getId())
            ->setParameter(5, DatumTypeEnum::TYPE_SIGN)
            ->setParameter(6, $user->getId())
        ;

        return $query->getArrayResult()[0];
    }
}
