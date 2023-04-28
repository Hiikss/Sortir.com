<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordFormType;
use App\Form\ProfilFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/*changed cause deprecated(modifier parameter "Security")*/


class ProfilController extends AbstractController
{
   /* #[Route('/profil', name: 'app_profil')]
    public function profil(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('profil/profil.html.twig', [
            'user' => $user,
        ]);
    }*/
    #[Route('/profil', name: 'app_profil')]
    public function modifier(#[CurrentUser] ?User $user,Request $request, EntityManagerInterface $entityManager): Response
    {
        //$user = $this->security->getUser();
        $form = $this->createForm(ProfilFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->getClickedButton() && 'cancel' === $form->getClickedButton()->getName()) {
            return $this->redirectToRoute('app_main');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $this->addFlash(
                'notice',
                'Vos changements on été sauvegardés');

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/modifier.html.twig', [
            'profilForm' => $form->createView(), 'user' => $user
        ]);
    }

    #[Route('/profil/mot-de-passe', name: 'app_profil_modify_password')]
    public function modifierMdp(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher, UserRepository $userRepo): Response
    {
        //$user = $this->security->getUser(); /* récupère l'user connecté */
        $form = $this->createForm(PasswordFormType::class);
        $form->handleRequest($request);

        //$userConnected = $this->getUser();



// test type de bouton
        // si le bouton submit est dans le twig getClickedButton() == null
        // si dans le form builder renvoie des données
        /*    if($form->isSubmitted()) {
                dd($form->getClickedButton());
            } */

        if ($form->isSubmitted() && $form->getClickedButton() && 'cancel' === $form->getClickedButton()->getName()) {
            return $this->redirectToRoute('app_profil');
        }
        //traiter le formulaire
        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $oldPassword = $data['oldPassword'];
            $userPassword = $user->getPassword();
            dump($oldPassword);
            dump($userPassword);
            if ($oldPassword == $userPassword) {
                // verifie si egal user->password == oldPassword
                $user->setPassword($data['password']);
              //  $passwordHash = $passwordHasher->hashPassword($user, $user->getPassword());
                //  $user->setPassword($passwordHash);

                //^ tout ça ici v avec Symfony ^6.2
                // $user = $form->getData();

                $entityManager->persist($user);
                $entityManager->flush();
           }
            return $this->redirectToRoute('app_profil');

            // sinon gerer l'erreur
            // ajouter erreur dans le form $form->adderror
            // rester dans la même page

        }

        return $this->render('profil/modifier_mdp.html.twig', [
            'profilForm' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/profil/{id}', name: 'profil_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('profil/show.html.twig', [
            'user' => $user
        ]);
    }

}


