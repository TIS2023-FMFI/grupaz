<?php

namespace App\Repository;

use App\Entity\CarGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByGid(string $gid): ?CarGroup
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.gid = :gid')
            ->setParameter('gid', $gid)
            ->getQuery()->getOneOrNullResult();
    }
    public function findToAdminApprove()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', 3)
            ->getQuery()->getResult();
    }

    public function findInProgress()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('(c.status = :openedGroup OR c.status = :startedScan)')
            ->setParameter('openedGroup', 1)
            ->setParameter('startedScan', 2)
            ->getQuery()->getResult();
    }


}
