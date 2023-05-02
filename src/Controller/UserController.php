<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/admin/user/list', name: 'admin_user_list')]
    public function list(UserRepository $userRepository, Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('searchzone', TextType::class, [
                'required' => false,
                'label' => 'Rechercher un utilisateur :',
                'attr' => [
                    'placeholder' => 'Chercher par nom ou email'
                ]
            ])->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $users = $userRepository->findByFilters($form->getData());
        }
        else {
            $users = $userRepository->findAll();
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'form' => $form
        ]);
    }

    #[Route('/admin/user/active/{id}', name: 'admin_user_active')]
    public function active(int $id, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $user = $userRepository->find($id);

        if($user) {
           $user->setActive(!$user->isActive());

           $em->persist($user);
           $em->flush();
        }

        return $this->redirectToRoute('admin_user_list');
    }

    #[Route('/admin/user/delete/{id}', name: 'admin_user_delete')]
    public function delete(int $id, UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        $user = $userRepository->find($id);

        if($user) {
           $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('admin_user_list');
    }
}
