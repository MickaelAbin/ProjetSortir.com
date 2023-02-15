<?php

namespace App\Controller;

use App\Entity\Etats;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\SortieType;
use App\Repository\EtatsRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request, SortieRepository $sortieRepository, UserRepository $userRepository, EtatsRepository $etatsRepository): Response
    {

        $user=$userRepository->find($this->getUser());
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $sortie->setSite($user->getSite());

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if($sortieForm->getClickedButton() === $sortieForm->get('Enregistrer')) {
                $sortie->setEtat($etatsRepository->findOneBy(['libelle'=>'créer']));
            }else{
                $sortie->setEtat($etatsRepository->findOneBy(['libelle'=>'ouverte']));
            }
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
    public function update(Request $request, Sortie $sortie, SortieRepository $sortieRepository): Response
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
    #[Route('/annuler/{id}', name: '_annuler')]
    public function annuler(Request $request, Sortie $sortie, SortieRepository $sortieRepository, EtatsRepository $etatsRepository): Response
    {

        $etat=($etatsRepository->find('2'));
        $sortie->setEtat($etat);
        $sortieRepository->save($sortie, true);
        return $this->redirectToRoute('sortie_index', []);
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
        UserRepository $userRepository,
        int $id,

    ): Response
    {
        $date = new \DateTime();
        $sortie = $sortieRepository->findOneBy(['id' => $id]);
        $user = $userRepository->find($this->getUser());
        if ($sortie->getDatecloture() > $date && $sortie->getEtat()->getLibelle() == 'ouverte') {
            $sortie->addParticipant($user);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('succes','Vous êtes bien inscrit');
                return $this->redirectToRoute('sortie_index', []);
        }
            $this->addFlash('echec','Vous ne pouvez pas vous inscrire à cette sortie');
                return $this->redirectToRoute('sortie_index', []);
    }
#[Route('/desister/{id}',name:'_desister')]
    public function desister(
        EntityManagerInterface $em,
        SortieRepository $sortieRepository,
        UserRepository $userRepository,
        int $id,

    ):Response
{
    $date = new \DateTime();
    $sortie = $sortieRepository->findOneBy(['id' => $id]);
    $user = $userRepository->find($this->getUser());
    if ($date < $sortie->getDatedebut()) {
        $sortie->removeParticipant($user);
        $em->persist($sortie);
        $em->flush();
        $this->addFlash('retrait', 'Vous ne participez plus à la sortie' . $sortie->getNom());
        return $this->redirectToRoute('sortie_index', []);
    }
    $this->addFlash('retrait', 'Vous ne pouvez pas vous désinscrire de la sortie: ' . $sortie->getNom());
    return $this->redirectToRoute('sortie_index', []);
}
}

