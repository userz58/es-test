<?php

namespace App\Controller;

use App\Entity\Region;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegionalCatalogController extends AbstractController
{
    #[Route('/region-{regionCode}/catalog/', name: 'app_regional_catalog_index', priority: 1)]
    public function index(
        #[MapEntity(mapping: ['regionCode' => 'code'])]
        Region $region
    ): Response
    {
        $suffixBuyInTheСity = sprintf('купить в %s', $region->getCase('prepositional'));
        $suffixDeliveryAtTheCity = sprintf('Доставка в %s', $region->getCase('accusative'));
        $suffixCalculateDeliveryToTheCity = sprintf('Рассчитать доставку до %s', $region->getCase('genitive'));

        // todo: catalog index categories

        return $this->render('catalog/regional/index.html.twig', [
            'region' => $region,
            'suffixBuyInTheСity' => $suffixBuyInTheСity,
            'suffixDeliveryAtTheCity' => $suffixDeliveryAtTheCity,
            'suffixCalculateDeliveryToTheCity' => $suffixCalculateDeliveryToTheCity,
        ]);
    }

    #[Route('/{regionCode}/catalog/{path}', name: 'app_regional_catalog_category', requirements: ['path' => '.+'], priority: 2)]
    public function category(
        #[MapEntity(mapping: ['regionCode' => 'code'])]
        Region $region,
        string $path
    ): Response
    {
        dump($region);
        dump($path);

        // todo: найти категорию
        // todo: проверить статус - скрыта или нет
        // todo: проверить статус - noindex

        die('stop CATEGORY action');
    }

    #[Route('/{regionCode}/catalog/{slug}', name: 'app_regional_catalog_product', requirements: ['slug' => '.+\.html$'], priority: 3)]
    public function product(
        #[MapEntity(mapping: ['regionCode' => 'code'])]
        Region $region,
        string $slug
    ): Response
    {
        dump($region);
        dump($slug);

        // todo: найти товар
        // todo: проверить статус - скрыт или нет
        // todo: проверить статус - noindex

        die('stop PRODUCT action');
    }

    #[Route('/{regionCode}/catalog/product-{productCode}.html', name: 'app_regional_catalog_canonical_product', priority: 4)]
    public function canonicalProduct(
        #[MapEntity(mapping: ['regionCode' => 'code'])]
        Region $region,
        string $productCode
    ): Response
    {
        dump($region);
        dump($productCode);

        // todo: найти тег
        // todo: проверить статус - скрыт или нет
        // todo: проверить статус - noindex

        die('stop CANONICAL PRODUCT action');
    }

    #[Route('/{regionCode}/catalog/tag-{slug}', name: 'app_regional_catalog_tag', priority: 5)]
    public function tag(
        #[MapEntity(mapping: ['regionCode' => 'code'])]
        Region $region,
        string $slug
    ): Response
    {
        dump($region);
        dump($slug);

        // todo: найти тег
        // todo: проверить статус - скрыт или нет
        // todo: проверить статус - noindex

        die('stop TAG action');
    }
}
