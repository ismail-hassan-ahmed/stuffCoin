<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Flasher\SweetAlert\Prime\SweetAlertBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ProfilType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', []);
    }

    #[Route('/profile/edit/{id}', name: 'profile_edit', methods:['GET', 'POST'])]
    public function edit(ManagerRegistry $doctrine,Request $request, User $user, UserPasswordHasherInterface $hasher): Response
    {   

        if (!$this->getUser()) {
            
            return $this->redirectToRoute('app_login');
        }
        $entityManager = $doctrine->getManager();
        $user = $this->getUser();
        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/edit.html.twig',[

            'profilForm' => $form->createView()
        ]);
    }
}
