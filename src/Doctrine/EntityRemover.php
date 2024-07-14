<?php

namespace App\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

class EntityRemover
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function remove(object $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
