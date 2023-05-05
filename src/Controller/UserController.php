<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CampusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/admin/user/list', name: 'admin_user_list')]
    public function list(UserRepository $userRepository, Request $request, SerializerInterface $serializer, CampusRepository $campusRespository, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $fileForm = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'required' => true,
                'label' => 'Importer des utilisateurs par fichier .csv',
                'attr' => [
                    'accept' => '.csv'
                ]
            ])->getForm();

        $fileForm->handleRequest($request);

        if ($fileForm->isSubmitted() && $fileForm->isValid()) {

            $data = $serializer->decode(file_get_contents($fileForm->get('file')->getData()), CsvEncoder::FORMAT);

            foreach ($data as $row) {
                $user = new User();
                $user->setEmail($row['email']);
                $user->setRoles(explode(",", $row['roles']));
                $user->setPassword($passwordHasher->hashPassword($user, $row['password']));
                $user->setPseudo($row['pseudo']);
                $user->setLastname($row['lastname']);
                $user->setFirstname($row['firstname']);
                $user->setTelephone($row['telephone']);
                $user->setActive($row['active']);
                $user->setCampus($campusRespository->findOneBy(['name' => $row['campus_name']]));

                $em->persist($user);
                $em->flush();
            }

            $this->addFlash(
                'success',
                'Les utilisateurs ont bien été importés !'
            );
            return $this->redirectToRoute('admin_user_list');
        }

        $searchForm = $this->createFormBuilder()
            ->add('searchzone', TextType::class, [
                'required' => false,
                'label' => 'Rechercher un utilisateur :',
                'attr' => [
                    'placeholder' => 'Chercher par nom ou email'
                ]
            ])->getForm();

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $users = $userRepository->findByFilters($searchForm->getData());
        }
        else {
            $users = $userRepository->findAll();
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'searchForm' => $searchForm,
            'fileForm' => $fileForm
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
    public function delete(int $id, UserRepository $userRepository, Request $request): Response
    {
        $user = $userRepository->find($id);

        $form = $this->createFormBuilder()->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $user) {
            $userRepository->remove($user, true);

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été supprimé !'
            );

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('user/delete.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }
}
