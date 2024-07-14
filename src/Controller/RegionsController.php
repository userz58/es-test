<?php

namespace App\Controller;

use App\Doctrine\Query\RegionQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegionsController extends AbstractController
{
    #[Route('/regions', name: 'app_regions')]
    public function index(RegionQuery $regionQuery): Response
    {
        // todo: перенести в настройки размер городов для выборки
        $regions = $regionQuery->getCitiesByPopulation(80000);

        return $this->render('regions/index.html.twig', [
            'regions' => $regions,
        ]);
    }
}
