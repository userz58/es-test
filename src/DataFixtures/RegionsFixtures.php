<?php

namespace App\DataFixtures;

use App\Builder\RegionBuilder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Города России
 * https://github.com/hflabs/city
 */
class RegionsFixtures extends Fixture
{
    private const FILEPATH = '%kernel.project_dir%/fixtures/regions.csv';
    private const BATCH_SIZE = 100;

    public function __construct(
        #[Autowire(self::FILEPATH)]
        private string                 $filepath,
        private SerializerInterface    $serializer,
        private RegionBuilder          $builder,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $content = file_get_contents($this->filepath);
        $data = $this->serializer->decode($content, 'csv');
        $i = 0;

        foreach ($data as $values) {
            $region = $this->builder->fromArray($values);

            $this->entityManager->persist($region);

            $i++;
            if (($i % self::BATCH_SIZE) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
                gc_collect_cycles();
            }
        }

        $this->entityManager->flush();
    }
}
