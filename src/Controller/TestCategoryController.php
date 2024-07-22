<?php

namespace App\Controller;

use Elastica\Query as Query;
use Elastica\QueryBuilder;
use JoliCode\Elastically\Client;
use App\Builder\Search\ProductModelBuilder;
use App\Entity\CatalogProduct;
use App\Entity\CatalogCategory;
use App\Repository\CatalogCategoryRepository;
use App\Repository\CatalogProductRepository;
use Elastica\Query\MultiMatch;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;


use JoliCode\Elastically\Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestCategoryController extends AbstractController
{

    public function __construct(
        private ProductModelBuilder $productModelBuilder,
    )
    {
    }

    #[Route('/test/category', name: 'app_test_trees')]
    public function index(CatalogCategoryRepository $repository): Response
    {
        $trees = $repository->getTrees();
        $root = $trees[2];
        //dump($root);

        $children = $repository->getChildren($root, false, 'left', 'ASC', true);

        return $this->render('test_category/index.html.twig', [
            'trees' => $trees,
            'root' => $root,
            'children' => $children,
        ]);
    }

    #[Route('/test/category/{id}', name: 'app_test_print_tree')]
    public function tree(
        CatalogCategory           $root,
        CatalogCategoryRepository $categoryRepository,
        CatalogProductRepository  $productRepository,
        Client                    $client,

    ): Response
    {
        $children = $categoryRepository->getChildren($root, false, 'left', 'ASC', true);

        /** @var @var CatalogProduct $product */
        //$products = $children[0]->getProducts();
        //foreach ($products as $product) {
        //    $model = $this->productModelBuilder->create($product);
        //    dump($model);
        //    die();
        //}

        //$ids = $categoryRepository->getAllChildrenIds($root, true);
        //dump($children);
        //die();
        //dump($ids);

        //$results = $client->getIndex('product')->getAliases();
        //dump($results);
        //die();

        // https://elastica.io/getting-started/search-documents.html
        // https://hotexamples.com/examples/elastica.query/Bool/-/php-bool-class-examples.html

        // https://www.youtube.com/watch?v=2KgJ6TQPIIA&list=PL_mJOmq4zsHZYAyK606y7wjQtC0aoE6Es&index=3&ab_channel=OfficialElasticCommunity
        // https://www.youtube.com/watch?v=iGKOdep1Iss&list=PL_mJOmq4zsHZYAyK606y7wjQtC0aoE6Es&index=5
        $searchText = 'printer';


        $query = new Query();
        $query->addSort(['updatedAt' => ['order' => 'DESC']]);

        $boolQuery = new BoolQuery();
        $boolQuery->addFilter(new Term(['enabled' => false]));

        if (null !== $searchText) {
            $searchQuery = new MultiMatch();
            $searchQuery->setFields([
                'name^5',
                'name.autocomplete',
                //'category.content',
                //'category.authorName',
            ]);
            $searchQuery->setQuery($searchText);
            $searchQuery->setType(MultiMatch::TYPE_MOST_FIELDS);
            //$searchQuery->setParams(['size' => 1,'from'=> 2]);
            //$searchQuery->addParam('sort', ['createdAt' => 'DESC']);

            $boolQuery->addMust($searchQuery);
        }

        //$boolQuery-> addSort(['created_at' => $sortDirection]);
        //$boolQuery->setParams(['size' => 1,'from'=> 2]);
$query->setQuery($boolQuery);
        $found = $client->getIndex('product')->search($query);
        //$found = $client->getIndex('product')->search($boolQuery);
        $results = [];
        foreach ($found->getResults() as $result) {
            dump($result->getModel());
        }
        die();


        $query = 'C752n';
        dump($query);
        $searchQuery = new MultiMatch();
        $searchQuery->setFields([
            'name^5',
            'name.autocomplete',
            //'category.content',
            //'category.authorName',
        ]);
        $searchQuery->setQuery($query);
        $searchQuery->setType(MultiMatch::TYPE_MOST_FIELDS);

        $found = $client->getIndex('product')->search($searchQuery);
        $results = [];
        foreach ($found->getResults() as $result) {
            dump($result->getModel());
        }

        die();


        return $this->render('test_category/printTree.html.twig', [
            'root' => $root,
            'children' => $children,
            'products' => []
        ]);
    }
}
