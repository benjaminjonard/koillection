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

    public function findOneByUsernameOrEmail($login): ?User
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

    public function getCounters(User $user): array
    {
        $filters = $this->getEntityManager()->getFilters();
        $visibilities = $filters->isEnabled('visibility') ? $filters->getFilter('visibility')->getVisibilities() : null;

        $collectionsSubQuery = 'SELECT COUNT(*) FROM koi_collection WHERE owner_id = ?';
        $itemsSubQuery = 'SELECT COUNT(*) FROM koi_item WHERE owner_id = ?';
        $tagsSubQuery = 'SELECT COUNT(*) FROM koi_tag WHERE owner_id = ?';
        $signsSubQuery = 'SELECT COUNT(*) FROM koi_datum d LEFT JOIN koi_item i ON i.id = d.item_id WHERE i.owner_id = ? AND d.type = ?';
        $params = [$user->getId(), $user->getId(), $user->getId(), $user->getId(), DatumTypeEnum::TYPE_SIGN];

        if (null !== $visibilities) {
            $collectionsSubQuery .= ' AND final_visibility IN (?)';
            $itemsSubQuery .= ' AND final_visibility IN (?)';
            $tagsSubQuery .= ' AND visibility IN (?)';
            $signsSubQuery .= ' AND d.final_visibility IN (?)';
            $params = [
                $user->getId(), $visibilities,
                $user->getId(), $visibilities,
                $user->getId(), $visibilities,
                $user->getId(), DatumTypeEnum::TYPE_SIGN, $visibilities,
            ];
        }

        $params[] = $user->getId();

        $sql = "SELECT ({$collectionsSubQuery}) AS collections, ({$itemsSubQuery}) AS items, ({$tagsSubQuery}) AS tags, ({$signsSubQuery}) AS signs";
        $sql .= ' FROM koi_user u';
        $sql .= ' WHERE u.id = ?';

        $rsm = new ResultSetMapping();
        $rsm
            ->addScalarResult('collections', 'collections')
            ->addScalarResult('items', 'items')
            ->addScalarResult('tags', 'tags')
            ->addScalarResult('signs', 'signs')
        ;

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        foreach ($params as $position => $value) {
            $query->setParameter($position + 1, $value);
        }

        return $query->getArrayResult()[0];
    }
}
