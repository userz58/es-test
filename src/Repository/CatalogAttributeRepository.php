<?php

namespace App\Repository;

use App\Entity\CatalogAttribute as Attribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CatalogAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }

    public function findByGroup(string $groupCode): array
    {
        $attributes = $this->findBy(['groupCode' => $groupCode], ['sortOrder' => 'ASC']);

        $groupped = [];

        foreach ($attributes as $attribute) {
            $groupped[$attribute->getGroupCode()][$attribute->getCode()] = $attribute;
        }

        return $groupped;
    }

    public function getNamesArray(): array
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.code, a.name')
            ->getQuery();

        $result = $query->getArrayResult();

        return array_combine(array_map(fn($i) => $i['code'], $result), array_map(fn($i) => $i['name'], $result));
    }

    public function getTypesArray(): array
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.code, a.type')
            ->getQuery();

        $result = $query->getArrayResult();

        return array_combine(array_map(fn($i) => $i['code'], $result), array_map(fn($i) => $i['type'], $result));
    }

    public function getGroupedCodesArray(): array
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.code, a.groupCode')
            ->getQuery();

        $result = [];
        foreach ($query->getArrayResult() as $attr) {
            $result[$attr['groupCode']][] = $attr['code'];
        }

        return $result;
    }

    public function getCodesByGroupCode($groupCode): array
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.code')
            ->where('a.groupCode = :groupCode')
            ->setParameter('groupCode', $groupCode)
            ->getQuery();

        return array_column($query->getArrayResult(), 'code');
    }

    public function getMediaTypeAttributesCodes(): array
    {
        $mediaTypes = ['pim_catalog_image', 'pim_catalog_file'];

        $query = $this->createQueryBuilder('a')
            ->select('a.code')
            ->where('a.type IN (:mediaTypes)')
            ->setParameter('mediaTypes', $mediaTypes)
            ->getQuery();

        return array_column($query->getArrayResult(), 'code');
    }

    public function getFilteredAttributeCodes(): array
    {
        $query = $this->createQueryBuilder('a')
            ->select('a.code')
            ->where('a.useInGrid = :filtered')
            ->setParameter('filtered', true)
            ->getQuery();

        return array_column($query->getArrayResult(), 'code');
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
