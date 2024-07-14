<?php

namespace App\DataFixtures;

use App\Entity\CatalogCategory as Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

class CatalogCategoryFixtures extends Fixture implements FixtureGroupInterface
{
    public const FILEPATH = '%kernel.project_dir%/fixtures/catalog/categories.csv';

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
        //usort($data, fn($a, $b) => strcmp($a['lft'], $b['lft'])); // отсортировать массив

        $categories = [];

        foreach ($data as $values) {
            $category = (new Category())
                ->setCode($values['code'])
                ->setName($values['name'])
                ->setUrl($values['url'])
                ->setValues(json_decode($values['values'], true))
            ;

            $categories[$values['id']] = $category;
        }

        foreach ($data as $values) {
            $category = $categories[$values['id']];

            if (!empty($values['parent_id'])) {
                $parent = $categories[$values['parent_id']];
                $parent->addChild($category);
            }

            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();
    }
}
