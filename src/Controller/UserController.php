<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MdpmodifierType;
use App\Form\ModifProfilType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_modifier')]
    public function modifier(
        FlashyNotifier              $flashy,
        Request                     $request,
        Security                    $security,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager,
        UserRepository              $userRepository
    ): Response
    {
//        $user = $this->getUser();
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        $form = $this->createForm(ModifProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && ($form->isValid()) ) {
//             Vérifiez si le mot de passe actuel est correct
                $currentPassword = $form->get('current_password')->getData();
                if (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {

//
                    $flashy->error(' Mauvais mot de passe actuel, modification(s) non effectuée(s) ');
                    return $this->redirectToRoute('user_modifier',);
                }

            // Mettez à jour les données de l'utilisateur



            $entityManager->persist($user);
            $entityManager->flush();
            $flashy->success(' Modification(s)  effectuée(s) ');
            return $this->redirectToRoute('user_details', ['id' => $user->getId()]);
        }
        return $this->render('user/modifier.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }
    #[Route('/mdp', name: 'mdp_modifier')]
    public function modifiermdp(
        FlashyNotifier              $flashy,
        Request                     $request,
        Security                    $security,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface      $entityManager,
        UserRepository              $userRepository
    ): Response
    {
//        $user = $this->getUser();
        $user = $userRepository->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);

        $form = $this->createForm(MdpmodifierType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && ($form->isValid()) ) {
//             Vérifiez si le mot de passe actuel est correct
            $currentPassword = $form->get('current_password')->getData();
            if (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {

//
                $flashy->error(' Mauvais mot de passe actuel, modification(s) non effectuée(s) ');
                return $this->redirectToRoute('mdp_modifier',);
            }

            // Mettez à jour les données de l'utilisateur

            $newPassword = $form->get('plainPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $userPasswordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);

            }

            $entityManager->persist($user);
            $entityManager->flush();
            $flashy->success(' Modification(s)  effectuée(s) ');
            return $this->redirectToRoute('user_details', ['id' => $user->getId()]);
        }
        return $this->render('mdp/modifier.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }
    #[
        Route('user/details/{id}', name: 'user_details')]
    public function details(
        int            $id,
        UserRepository $userRepository
    ): Response
    {
        $user = $userRepository->findOneBy(["id" => $id]);
        return $this->render(
            'user/details.html.twig',
            compact('user')
        );
    }

}