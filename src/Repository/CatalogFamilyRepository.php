<?php

namespace App\Repository;

use App\Entity\CatalogFamily as Family;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CatalogFamilyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Family::class);
    }

    public function getNotInArrayCodes(array $codes): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.code NOT IN (:codes)')
            ->setParameter('codes', $codes)
            ->getQuery()
            ->getResult();
    }
}
