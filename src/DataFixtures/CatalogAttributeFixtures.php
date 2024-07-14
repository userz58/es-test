<?php

namespace App\DataFixtures;

use App\Entity\CatalogAttribute as Attribute;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

class CatalogAttributeFixtures extends Fixture implements FixtureGroupInterface
{
    public const FILEPATH = '%kernel.project_dir%/fixtures/catalog/attributes.csv';
    private const BATCH_SIZE = 100;

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
        $i = 0;

        foreach ($data as $values) {
            $i++;

            // todo: --> перенести в factory
            $attribute = (new Attribute())
                ->setCode($values['code'])
                ->setType($values['type'])
                ->setGroupCode($values['group_code'])
                ->setName($values['name'])
                ->setDescription($values['description'])
                ->setSortOrder($values['sort_order'])
                ->setUseInGrid($values['use_in_grid'])
                ->setLocalizable($values['localizable'])
                ->setScopable($values['scopable'])
                ->setWysiwygEnabled($values['wysiwyg_enabled'])
            ;
            // unset($values['id']);
            // $attribute = $this->factory->fromArray($values);
            // todo: <--

            $this->entityManager->persist($attribute);

            if (($i % self::BATCH_SIZE) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
                //gc_collect_cycles();
            }
        }

        $this->entityManager->flush();
    }
}
