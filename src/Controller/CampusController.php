<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/campus', name: 'admin_campus_')]
class CampusController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(CampusRepository $campusRepository, Request $request, EntityManagerInterface $em): Response
    {
        $campus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $campus);

        $campusForm->handleRequest($request);

        if ($campusForm->isSubmitted() && $campusForm->isValid()) {
            $em->persist($campus);
            $em->flush();
        }

        $searchForm = $this->createFormBuilder()
            ->add('searchzone', TextType::class, [
                'required' => false,
                'label' => 'Le nom contient :',
                'attr' => [
                    'placeholder' => 'Chercher'
                ]
        ])->getForm();

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $campus = $campusRepository->findByFilters($searchForm->getData());
        }
        else {
            $campus = $campusRepository->findAll();
        }

        return $this->render('campus/index.html.twig', [
            'campus' => $campus,
            'campusForm' => $campusForm,
            'searchForm' => $searchForm
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(int $id, CampusRepository $campusRepository): Response
    {
        $campus = $campusRepository->find($id);

        if($campus) {
           $campusRepository->remove($campus, true);
        }

        return $this->redirectToRoute('admin_campus_list');
    }
}
