<?php

namespace App\Repository;

use App\Entity\Region;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RegionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Region::class);
    }

    public function getCitiesByPopulation(int $count = 100000): array
    {
        $query = $this->createQueryBuilder('r')
            ->andWhere('r.population > :count')
            ->setParameter('count', $count)
            ->addOrderBy(' r.population', 'DESC')
            ->getQuery();

        return $query->getResult();
    }
/*
    public function getFederalDistricts(): array
    {
        $query = $this->createQueryBuilder('r')
            ->select(' r.federalDistrict')
            ->groupBy('r.federalDistrict')
            ->getQuery();

        return $query->getSingleColumnResult();
    }

    public function getCitiesByFederalDistrict(string $federalDistrict)
    {
        $query = $this->createQueryBuilder('r')
            ->andWhere('r.federalDistrict = :district')
            ->setParameter('district', $federalDistrict)
            ->addOrderBy(' r.population', 'DESC')
            ->getQuery();

        return $query->getResult();
    }

    public function iterateAll(): iterable
    {
        return $this->createQueryBuilder('r')->getQuery()->toIterable();
    }
*/
}
