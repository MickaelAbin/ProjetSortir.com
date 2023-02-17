<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user_modifier')]
    public function modifier( Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();


        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('succes',' Modification(s) effectuée(s) ');

            return $this->redirectToRoute('user_details',['id' => $user->getId()]);
        }
        $this->addFlash('echec',' Modification(s) non effectuée(s) ');
        return $this->render('user/modifier.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    #[Route('user/details/{id}', name: 'user_details')]
    public function details(
        int             $id,
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
