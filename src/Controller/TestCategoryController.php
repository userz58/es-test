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
use Elastica\Query\MatchQuery;
use Elastica\Query\MultiMatch;
use Elastica\Query\BoolQuery;
use Elastica\Query\Range;
use Elastica\Query\Term;
use Elastica\Query\Nested;


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

    #[Route('/test/trees', name: 'app_test_trees')]
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

    #[Route('/test/tree/{id}', name: 'app_test_print_tree')]
    public function tree(CatalogCategory $root, CatalogCategoryRepository $categoryRepository, CatalogProductRepository $productRepository): Response
    {
        $children = $categoryRepository->getChildren($root, false, 'left', 'ASC', true);

        return $this->render('test_category/printTree.html.twig', [
            'root' => $root,
            'children' => $children,
            'products' => []
        ]);
    }

    #[Route('/test/category/{id}', name: 'app_test_print_category')]
    public function category(CatalogCategory $category, CatalogCategoryRepository $categoryRepository, Client $client): Response
    {
        dump($category);

        $boolQuery = (new BoolQuery())
            ->addFilter(new MatchQuery('categories.root', $category->getRoot()->getId())) // дерево этой категории
            ->addFilter(new Range('categories.left', ['gte' => $category->getLeft()])) // все вложенные подкатегории
            ->addFilter(new Range('categories.right', ['lte' => $category->getRight()])) // все вложенные подкатегории
        ;

        $nestedQuery = (new Nested())
            ->setPath('categories')
            ->setQuery($boolQuery)
        ;

        $query = new Query();
        $query->setSize(50);
        $query->setFrom(0);
        $query->addSort(['updatedAt' => ['order' => 'DESC']]);
        $query->setQuery($nestedQuery);

        // todo: фильтр покажет сколько товаров в категории
        // todo: пост фильтр покажет сколько товаров в каждой подкатегории

        //$query->addAggregation()
        //$query->setPostFilter()
        //dump($boolQuery->toArray());
        dump($nestedQuery->toArray());
        dump($query->toArray());

        $found = $client->getIndex('product')->search($query);
        $products = array_map(fn($r) => $r->getModel(), $found->getResults());


        // https://elastica.io/getting-started/search-documents.html
        // https://hotexamples.com/examples/elastica.query/Bool/-/php-bool-class-examples.html

        // https://www.youtube.com/watch?v=2KgJ6TQPIIA&list=PL_mJOmq4zsHZYAyK606y7wjQtC0aoE6Es&index=3&ab_channel=OfficialElasticCommunity
        // https://www.youtube.com/watch?v=iGKOdep1Iss&list=PL_mJOmq4zsHZYAyK606y7wjQtC0aoE6Es&index=5

        /*
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
        */

        return $this->render('test_category/printCategory.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
