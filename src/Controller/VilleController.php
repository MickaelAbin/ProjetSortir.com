<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/ville', name: 'ville')]
class VilleController extends AbstractController
{
    #[Route('/create', name: '_create')]
    public function index(
        Request $request
    ): Response
    {

        if ($request->get('creer') !== null) {

        }

        return $this->render('ville/create.html.twig', []);
    }
}
