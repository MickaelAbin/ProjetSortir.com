<?php

namespace App\Controller;

use App\Entity\Etats;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\FiltreType;
use App\Form\SortieType;
use App\Repository\EtatsRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(
        SortieRepository $sortieRepository,
        SiteRepository $siteRepository,
        Request $request,
        PaginatorInterface $paginator,
        UserRepository $userRepository
    ): Response
    {
        $filtreForm = $this->createForm(FiltreType::class);
        $filtreForm->handleRequest($request);
        $user = $userRepository->find($this->getUser());

        if ($filtreForm->isSubmitted()) {
            $sortie = $sortieRepository->findSortieWithFiltre($filtreForm->getData(), $user);
        }else {
            $filtreForm->setData([
                'site' => $user->getSite(),
                'recherche' => '',
                'dateDepart' => new \DateTime(),
                'dateFin' => null,
                'organise' => false,
                'inscrit' => false,
                'nonInscrit' => false,
                'passe' => false
            ]);
            $sortie = $sortieRepository->findSortieWithFiltre($filtreForm->getData(), $user);
        }

        $affichage=$paginator->paginate(
            $sortie,
            $request->query->getInt('page',1),
            8
        );

        return $this->render('sortie/index.html.twig', [
            'sorties' => $affichage,
            'sites' => $siteRepository->findAll(),
            'filtreForm' => $filtreForm
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(
        FlashyNotifier $flashy,
        Request $request,
        SortieRepository $sortieRepository,
        UserRepository $userRepository,
        EtatsRepository $etatsRepository
    ): Response
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
                $flashy->success(' Sortie créée ');

            }else{
                $sortie->setEtat($etatsRepository->findOneBy(['libelle'=>'ouverte']));
                $flashy->success(' Sortie publiée ');
            }
            $sortieRepository->save($sortie, true);
            return $this->redirectToRoute('sortie_index', []);
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm
        ]);
    }
    #[Route('/publier/{id}', name: '_publier')]
    public function publier(
        FlashyNotifier $flashy,
        Request $request,
        Sortie $sortie,
        SortieRepository $sortieRepository,
        EtatsRepository $etatsRepository
    ): Response
    {
                $sortie->setEtat($etatsRepository->findOneBy(['libelle'=>'ouverte']));
                $sortieRepository->save($sortie, true);
                $flashy->success(' Sortie publiée ');
                return $this->redirectToRoute('sortie_index', []);
        }


    #[Route('/detail/{id}', name: '_detail')]
    public function detail(
        int $id,
        SortieRepository $sortieRepository
    ): Response
    {

        return $this->render('sortie/detail.html.twig', [
            'sortie' => $sortieRepository->findDetailSortie($id)[0],
        ]);
    }

    #[Route('/update/{id}', name: '_update')]
    public function update(
        Request $request,
        Sortie $sortie,
        SortieRepository $sortieRepository,
        EtatsRepository $etatsRepository
    ): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->getClickedButton() === $form->get('Enregistrer')) {
                $sortie->setEtat($etatsRepository->findOneBy(['libelle'=>'créer']));
            }else{
                $sortie->setEtat($etatsRepository->findOneBy(['libelle'=>'ouverte']));
            }
            $sortieRepository->save($sortie, true);
            return $this->redirectToRoute('sortie_index', []);
        }

        return $this->render('sortie/update.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }
    #[Route('/annuler/{id}', name: '_annuler')]
    public function annuler(FlashyNotifier $flashy,Request $request, Sortie $sortie, SortieRepository $sortieRepository, EtatsRepository $etatsRepository): Response
    {

        $etat=($etatsRepository->findOneBy(['libelle' => 'annulée']));
        $sortie->setEtat($etat);
        $sortieRepository->save($sortie, true);
        $flashy->success(' Sortie annulée ');
        return $this->redirectToRoute('sortie_index', []);
    }

    #[Route('/detailannulation/{id}', name: '_detailannulation')]
    public function detailannulation(
        int $id,
        EtatsRepository $etatsRepository,
        SortieRepository $sortieRepository,
        Sortie $sortie,
        Request $request,
        FlashyNotifier $flashy
    ): Response
    {
        if ($sortie->getDatedebut() <= new \DateTime()) {
            $flashy->error('Vous ne pouvez pas annulé la sortie!');
            return $this->redirectToRoute('sortie_index', []);
        } else if ($request->get("valide") !== null){

            $sortie = $sortieRepository->find($id);
            $etat = ($etatsRepository->findOneBy(['libelle' => 'annulée']));
            $sortie->setEtat($etat);
            $sortie->setDescriptioninfos($request->get('motif'));
            $sortieRepository->save($sortie, true);
            return $this->redirectToRoute('sortie_index', []);
        }

            return $this->render('sortie/detailannulation.html.twig', [

                'sortie' => $sortieRepository->findDetailSortie($id)[0],
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
        FlashyNotifier              $flashy,
        EntityManagerInterface $em,
        SortieRepository $sortieRepository,
        UserRepository $userRepository,
        int $id,

    ): Response
    {
        $date = new \DateTime();
        $sortie = $sortieRepository->findOneBy(['id' => $id]);
        $user = $userRepository->find($this->getUser());
        if ($sortie->getDatecloture() > $date && $sortie->getEtat()->getLibelle() == 'ouverte' && $sortie->getNbinscriptionsmax()>$sortie->getParticipants()->count()) {
            $sortie->addParticipant($user);
            $em->persist($sortie);
            $em->flush();
            $flashy->success(' Inscription validée ');
                return $this->redirectToRoute('sortie_index', []);
        }
            $flashy->error('Vous ne pouvez pas vous inscrire à cette sortie');
                return $this->redirectToRoute('sortie_index', []);
    }
    #[Route('/desister/{id}',name:'_desister')]
        public function desister(
            FlashyNotifier              $flashy,
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
            $flashy->success(' Désistement validé ');
            return $this->redirectToRoute('sortie_index', []);
        }
        $flashy->error('Vous ne pouvez pas vous désinscrire de la sortie');

        return $this->redirectToRoute('sortie_index', []);
    }

    #[Route('/donnees', name: '_donnees')]
    public function donnees(
        SortieRepository $sortieRepository,
    ){
        $sorties = $sortieRepository->findAll();

        $lieus = array();

        foreach ($sorties as $sortie){
            $coords = [$sortie->getLieu()->getLongitude(),$sortie->getLieu()->getLatitude()];
            $lieu = ['nom' => $sortie->getLieu()->getNomLieu(), 'coord' => $coords];
            $lieus[] = $lieu;
        }

        return new JsonResponse($lieus);
    }

    #[Route('/carte', name: '_carte')]
    public function carte(
        SortieRepository $sortieRepository,
        UserRepository $userRepository
    ){

        return $this->render('sortie/carte.html.twig');
    }

}

