<?php

namespace App\DataFixtures;

use App\Entity\CatalogFamily as Family;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

class CatalogFamilyFixtures extends Fixture implements FixtureGroupInterface
{
    public const FILEPATH = '%kernel.project_dir%/fixtures/catalog/families.csv';

    public function __construct(
        #[Autowire(self::FILEPATH)]
        private string                 $filepath,
        private SerializerInterface    $serializer,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    public static function getGroups(): array
    {
        return ['test', 'catalog'];
    }

    public function load(ObjectManager $manager): void
    {
        $content = file_get_contents($this->filepath);
        $data = $this->serializer->decode($content, 'csv');

        foreach ($data as $values) {
            $family = (new Family())
                ->setCode($values['code'])
                ->setName($values['name'])
                ->setAttributeAsLabel($values['attribute_as_label'])
                ->setAttributeAsImage($values['attribute_as_image'])
            ;

            $this->entityManager->persist($family);
        }

        $this->entityManager->flush();
    }
}
