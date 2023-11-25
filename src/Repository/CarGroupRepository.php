<?php

namespace App\Repository;

use App\Entity\CarGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CarGroup>
 *
 * @method CarGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarGroup[]    findAll()
 * @method CarGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarGroup::class);
    }

    public function findOneByGid(string $gid): ?CarGroup
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.gid = :gid')
            ->setParameter('gid', $gid)
            ->getQuery()->getOneOrNullResult();
    }

//    /**
//     * @return CarGroup[] Returns an array of CarGroup objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CarGroup
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
