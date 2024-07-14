<?php

namespace App\DataFixtures;


use App\Repository\CatalogCategoryRepository;
use App\Entity\CatalogProduct as Product;
use App\Entity\CatalogFamily as Family;
use App\Entity\CatalogCategory as Category;
use App\Repository\CatalogFamilyRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\SerializerInterface;

class CatalogProductFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    public const FILEPATH = '%kernel.project_dir%/fixtures/catalog/products.csv';
    public const FILEPATH_REF = '%kernel.project_dir%/fixtures/catalog/product_category_references.csv';
    private const BATCH_SIZE = 10;

    public function __construct(
        #[Autowire(self::FILEPATH)]
        private string                    $filepath,
        #[Autowire(self::FILEPATH_REF)]
        private string                    $filepathRef,
        #[Autowire(CatalogFamilyFixtures::FILEPATH)]
        private string                    $filepathFamilies,
        #[Autowire(CatalogCategoryFixtures::FILEPATH)]
        private string                    $filepathCategories,
        private CatalogFamilyRepository   $familyRepository,
        private CatalogCategoryRepository $categoryRepository,
        private SerializerInterface       $serializer,
        private EntityManagerInterface    $entityManager,
    )
    {
    }

    public function getDependencies(): array
    {
        return [CatalogAttributeFixtures::class, CatalogFamilyFixtures::class, CatalogCategoryFixtures::class];
    }

    public static function getGroups(): array
    {
        return ['test', 'catalog'];
    }


    public function load(ObjectManager $manager): void
    {
        $familiesCodes = $this->getFamilyCodes();
        $productRef = $this->getReferences();
        $i = 0;

        foreach ($this->readFileToArray($this->filepath) as $values) {
            $i++;

            $product = (new Product())
                ->setCode($values['code'])
                ->setSku($values['sku'])
                ->setName($values['name'])
                ->setImage($values['image'])
                ->setEnable((bool)$values['is_enabled'])
                ->setValues(json_decode($values['values'], true))
                ->setAssociations(json_decode($values['associations'], true));

            if ("" !== $familyId = $values['family_id']) {
                $familiesCode = $familiesCodes[$familyId];
                if (null !== $family = $this->familyRepository->findOneByCode($familiesCode)) {
                    $product->setFamily($family);
                }
            }

            $categories = $this->categoryRepository->findBy(['code' => $productRef[$values['id']]]);
            foreach ($categories as $category) {
                $product->addCategory($category);
            }

            $this->entityManager->persist($product);

            if (($i % self::BATCH_SIZE) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
                gc_collect_cycles();
            }
        }

        $this->entityManager->flush();
    }

    private function getFamilyCodes(): array
    {
        $result = [];

        foreach ($this->readFileToArray($this->filepathFamilies) as $values) {
            $result[$values['id']] = $values['code'];
        }

        return $result;
    }

    private function getReferences(): array
    {
        $categories = [];

        foreach ($this->readFileToArray($this->filepathCategories) as $values) {
            $categories[$values['id']] = $values['code'];
        }

        $result = [];

        foreach ($this->readFileToArray($this->filepathRef) as $values) {
            if (!isset($result[$values['product_id']])) {
                $result[$values['product_id']] = [];
            }

            $categoryCode = $categories[$values['category_id']];
            $result[$values['product_id']][] = $categoryCode;
        }

        return $result;
    }

    private function readFileToArray(string $filepath): array
    {
        return $this->serializer->decode(file_get_contents($filepath), 'csv');
    }
}
