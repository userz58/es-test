<?php

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

class EntitySaver
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function save(object $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
