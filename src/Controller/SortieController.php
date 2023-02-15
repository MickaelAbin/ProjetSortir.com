<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\User;
use App\Form\FiltreType;
use App\Form\SortieType;
use App\Repository\EtatsRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Service\SortieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(
        SortieRepository $sortieRepository,
        SiteRepository $siteRepository,
        Request $request
    ): Response
    {
        $filtreForm = $this->createForm(FiltreType::class);
        $filtreForm->handleRequest($request);

        if ($filtreForm->isSubmitted()) {
            dump($filtreForm->getData());
            $sortie = (new SortieService($sortieRepository))->findSortieWithFiltre($filtreForm, $this->getUser()->getUserIdentifier());
        }else {
            $sortie = $sortieRepository->findAll();
        }
        dump($sortie);
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortie,
            'sites' => $siteRepository->findAll(),
            'filtreForm' => $filtreForm
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(
        Request $request,
        SortieRepository $sortieRepository,
        UserRepository $userRepository,
        EtatsRepository $etatsRepository
    ): Response
    {
        $etat=$etatsRepository->findOneBy(['id'=>1]);
        $user=$userRepository->find($this->getUser());
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $sortie->setSite($user->getSite());
        $sortie->setEtat($etat);
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);


        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('sortie_index', []);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm
        ]);
    }

    #[Route('/detail/{id}', name: '_detail')]
    public function detail(
        int $id,
        SortieRepository $sortieRepository
    ): Response
    {
        dump($sortieRepository->findDetailSortie($id));
        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortieRepository->findDetailSortie($id)[0],
        ]);
    }

    #[Route('/update/{id}', name: '_update')]
    public function update(
        Request $request,
        Sortie $sortie,
        SortieRepository $sortieRepository
    ): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('sortie_index', []);
        }

        return $this->render('sortie/update.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/admin/delete/{id}', name: '_delete')]
    public function delete(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $sortieRepository->remove($sortie, true);
        }

        return $this->redirectToRoute('app_sortie_index', []);
    }

    #[Route('/inscription/{id}', name: '_inscription')]
    public function inscription(
        EntityManagerInterface $em,
        SortieRepository $sortieRepository,
        User $user,
        int $id
    ): Response
    {
        $sortie = $sortieRepository->findOneBy(['id'=>$id]);
        $sortie->addParticipant($user);
        $sortieRepository->save($sortie);
        $em->persist($sortie);
        $em->flush();
        return $this->redirectToRoute('sortie_index',[]);
    }
}

