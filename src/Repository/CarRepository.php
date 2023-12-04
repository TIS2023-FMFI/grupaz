<?php

namespace App\Repository;

use App\Entity\Car;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Car>
 *
 * @method Car|null find($id, $lockMode = null, $lockVersion = null)
 * @method Car|null findOneBy(array $criteria, array $orderBy = null)
 * @method Car[]    findAll()
 * @method Car[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }


    /**
     * @throws NonUniqueResultException
     */
    public function findOneByVis(string $vis): ?Car
    {
        return $this->createQueryBuilder('car')
            ->andWhere('car.vis = :vis')
            ->setParameter('vis', $vis)
            ->getQuery()->getOneOrNullResult();
    }

    public function findByFormData(DateTimeInterface $start, DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('car')
            ->select('car_group, car')
            ->leftJoin('car.carGroup', 'car_group')
            ->andWhere('car_group.exportTime >= :start')
            ->setParameter('start', $start)
            ->andWhere('car_group.exportTime <= :end')
            ->setParameter('end', $end)
            ->orderBy('car_group.exportTime', 'DESC')
            ->getQuery()->getResult();
    }

    public function deleteByFormData(DateTimeInterface $startTime, DateTimeInterface $endTime)
    {
        return $this->createQueryBuilder('c')
            ->delete('App\Entity\CarGroup', 'car_group')
            ->andWhere('car_group.exportTime >= :startTime')
            ->setParameter('startTime', $startTime)
            ->andWhere('car_group.exportTime <= :endTime')
            ->setParameter('endTime', $endTime)
            ->getQuery()->execute();
    }

    public function findOneByVisInGroup(int $id, string $vis): ?Car
    {
        return $this->createQueryBuilder('car')
            ->andWhere('car.vis = :vis')
            ->setParameter('vis', $vis)
            ->andWhere('car.carGroup = :id')
            ->setParameter('id', $id)
            ->getQuery()->getOneOrNullResult();
    }

    public function countAllLoaded(int $id): int
    {
        return $this->createQueryBuilder('car')
            ->select('COUNT(car.vis)')
            ->andWhere('car.carGroup = :id')
            ->setParameter('id', $id)
            ->andWhere('car.status = :status')
            ->setParameter('status', 1)
            ->getQuery()->getSingleScalarResult();
    }
}
