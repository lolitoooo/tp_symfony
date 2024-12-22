<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 *
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function search(string $query): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'c')
            ->where('LOWER(a.name) LIKE LOWER(:query)')
            ->orWhere('LOWER(a.description) LIKE LOWER(:query)')
            ->orWhere('LOWER(c.name) LIKE LOWER(:query)')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
