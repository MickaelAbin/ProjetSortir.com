<?php

namespace App\Controller;

use App\Entity\Sortie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/meteo', name: 'meteo')]
class MeteoController extends AbstractController
{
    #[Route('/detail/{id}', name: '_detail')]
    public function detail(
        Sortie $sortie
    ): Response
    {

        $latitude = $sortie->getLieu()->getLatitude();
        $longitude = $sortie->getLieu()->getLongitude();
        $dateSortie = $sortie->getDatedebut();

        return $this->render('meteo/detail.html.twig', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'dateDebut' => $dateSortie
        ]);
    }
}
