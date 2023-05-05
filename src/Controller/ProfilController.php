<?php

namespace App\Controller;

use App\Entity\User;
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
    #[Route('/profil', name: 'app_profil')]
    public function modifier(#[CurrentUser] ?User $user,Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfilFormType::class, $user);
        $form->handleRequest($request);

       /* if ($form->isSubmitted() && $form->getClickedButton() && 'cancel' === $form->getClickedButton()->getName()) {
            return $this->redirectToRoute('app_main');
        } */

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
            'profilForm' => $form->createView(), 'user' => $user,
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


