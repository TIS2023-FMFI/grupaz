<?php

namespace App\Repository;

use App\Entity\HistoryCarGroup;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<HistoryCarGroup>
 *
 * @method HistoryCarGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoryCarGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoryCarGroup[]    findAll()
 * @method HistoryCarGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryCarGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoryCarGroup::class);
    }

    public function deleteByFormData(DateTimeInterface $startTime, DateTimeInterface $endTime): int
    {
        $carGroupIds = $this->createQueryBuilder('cg')
            ->select('cg.id')
            ->andWhere('cg.exportTime >= :fromTime')
            ->andWhere('cg.exportTime <= :toTime')
            ->setParameter('fromTime', $startTime)
            ->setParameter('toTime', $endTime)
            ->getQuery()
            ->getResult();
        $this->getEntityManager()->beginTransaction();
        try {
            $this->createQueryBuilder('cg')
                ->delete('App\Entity\HistoryCar', 'car')
                ->where('car.historyCarGroup IN (:carGroupIds)')
                ->setParameter('carGroupIds', $carGroupIds)
                ->getQuery()
                ->execute();
            $result = $this->createQueryBuilder('cg')
                ->delete('App\Entity\HistoryCarGroup', 'carGroup')
                ->where('carGroup.exportTime >= :fromTime')
                ->andWhere('carGroup.exportTime <= :toTime')
                ->setParameter('fromTime', $startTime)
                ->setParameter('toTime', $endTime)
                ->getQuery()
                ->execute();
            $this->getEntityManager()->commit();
            return $result;
        } catch (Exception) {
            $this->getEntityManager()->rollback();
            return -1;
        }
    }
}
