<?php

namespace App\Controller\Catalog;

use App\Repository\CatalogCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogIndexController extends AbstractController
{
    #[Route('/catalog', name: 'app_catalog_index')]
    public function index(CatalogCategoryRepository $repository): Response
    {
        return $this->render('catalog/index.html.twig', [
            //'trees' => $trees,
            //'root' => $root,
            //'children' => $children,
        ]);
    }
}
