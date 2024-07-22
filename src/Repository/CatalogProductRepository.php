<?php

namespace App\Repository;

use App\Entity\CatalogProduct as Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class CatalogProductRepository extends ServiceEntityRepository
{
    public const ALIAS_CATEGORIES = 'categories';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }


    public function iterareAll(): iterable
    {
        return $this->createQueryBuilder('p')->getQuery()->toIterable();
    }

    protected function getRootAlias(QueryBuilder $queryBuilder): string
    {
        $aliases = $queryBuilder->getRootAliases();

        return $aliases[0];
    }

    protected function addCriteriaEnabled(QueryBuilder $queryBuilder): QueryBuilder
    {
        $rootAlias = $this->getRootAlias($queryBuilder);

        return $queryBuilder
            ->andWhere(sprintf('%s.isEnabled = :enabled', $rootAlias))
            ->setParameter('enabled', true);
    }

    protected function addCriteriaIdsInArray(array $ids, QueryBuilder $queryBuilder): QueryBuilder
    {
        $rootAlias = $this->getRootAlias($queryBuilder);

        //$expr = $queryBuilder->expr()->in(sprintf('%s.id', $rootAlias), ':ids');
        //return $queryBuilder->andWhere($expr)->setParameter('ids', $ids);

        return $queryBuilder
            ->andWhere(sprintf('%s.id IN (:ids)', $rootAlias))
            ->setParameter('ids', $ids);
    }

    protected function addCriteriaCodesInArray(array $codes, QueryBuilder $queryBuilder): QueryBuilder
    {
        $rootAlias = $this->getRootAlias($queryBuilder);

        //$expr = $queryBuilder->expr()->in(sprintf('%s.code', $rootAlias), ':codes');
        //return $queryBuilder->andWhere($expr)->setParameter('codes', $codes);

        return $queryBuilder
            ->andWhere(sprintf('%s.code IN (:codes)', $rootAlias))
            ->setParameter('codes', $codes);
    }

    protected function addCriteriaCodeEqual(string $code, QueryBuilder $queryBuilder): QueryBuilder
    {
        $rootAlias = $this->getRootAlias($queryBuilder);

        return $queryBuilder
            ->andWhere(sprintf('%s.code = :code', $rootAlias))
            ->setParameter('code', $code);
    }

    protected function addJoinCategories(QueryBuilder $queryBuilder): QueryBuilder
    {
        $rootAlias = $this->getRootAlias($queryBuilder);

        return $queryBuilder
            ->leftJoin(sprintf('%s.categories', $rootAlias), self::ALIAS_CATEGORIES)
            ->addSelect(self::ALIAS_CATEGORIES);
    }

    protected function addOrderByLatestUpdated(QueryBuilder $queryBuilder): QueryBuilder
    {
        $rootAlias = $this->getRootAlias($queryBuilder);

        return $queryBuilder
            ->orderBy(sprintf('%s.updatedAt', $rootAlias), 'DESC');
    }

    public function findEnabledByIds(array $ids): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb = $this->addCriteriaIdsInArray($ids, $qb);
        $qb = $this->addCriteriaEnabled($qb);
        $qb = $this->addJoinCategories($qb);
        $qb = $this->addOrderByLatestUpdated($qb);

        return $qb->getQuery()->getResult();
    }

    public function findEnabledByCodes(array $codes): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb = $this->addCriteriaCodesInArray($codes, $qb);
        $qb = $this->addCriteriaEnabled($qb);
        $qb = $this->addOrderByLatestUpdated($qb);
        $qb = $this->addJoinCategories($qb);

        return $qb->getQuery()->getResult();
    }

    /*
    public function getEnabledCountByCodes(array $codes): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('count(p.id)');
        $qb = $this->addCriteriaCodesInArray($codes, $qb);
        $qb = $this->addCriteriaEnabled($qb);

        return $qb->getQuery()->getSingleScalarResult();
    }
    */

    /*
    public function isEnabledByCode(string $code): bool
    {
        $qb = $this->createQueryBuilder('p')
            ->select('count(p.id)');
        $qb = $this->addCriteriaCodeEqual($code, $qb);
        $qb = $this->addCriteriaEnabled($qb);

        $count = $qb->getQuery()->getSingleScalarResult();

        if ($count > 0) {
            return true;
        }

        return false;
    }
    */

//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
