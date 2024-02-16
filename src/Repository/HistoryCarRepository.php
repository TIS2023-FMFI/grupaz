<?php

namespace App\Repository;

use App\Entity\HistoryCar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoryCar>
 *
 * @method HistoryCar|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryCar|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryCar[]    findAll()
 * @method HistoryCar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryCarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryCar::class);
    }

//    /**
//     * @return HistoryCar[] Returns an array of HistoryCar objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HistoryCar
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
