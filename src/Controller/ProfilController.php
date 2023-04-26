<?php

namespace App\Controller;

use App\Form\PasswordFormType;
use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route; /*changed cause deprecated(modifier parameter "Security")*/


class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('profil/profil.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/profil/modifier', name: 'app_profil_modify')]
    public function modifier(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        $form = $this->createForm(ProfilFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/modifier.html.twig', [
            'profilForm' => $form->createView()
        ]);
    }

    #[Route('/profil/mot-de-passe', name: 'app_profil_modify_password')]
    public function modifierNom(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher, Security $security): Response
    {
        $user = $security->getUser(); /* récupère l'user connecté */
        $form = $this->createForm(PasswordFormType::class, $user);
        $form->handleRequest($request);


        //traiter le formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $user= $form->getData();
            $passwordHash = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($passwordHash);

            //^ tout ça ici v avec Symfony ^6.2
            // $user = $form->getData();

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/modifier.html.twig', [
            'profilForm' => $form->createView()
        ]);
    }

}


