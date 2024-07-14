<?php

namespace App\Doctrine\Query;

use App\Repository\RegionRepository;

class RegionQuery
{
    public function __construct(
        private RegionRepository $repository,
    )
    {
    }

    public function getCitiesByPopulation(int $count): array
    {
        return $this->repository->getCitiesByPopulation($count);
    }

    public function all(): iterable
    {
        return $this->repository->iterateAll();
    }
}
