<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaceController extends AbstractController
{
    #[Route('/place/list', name: 'place_list')]
    public function new(Request $request, EntityManagerInterface $em, PlaceRepository $placeRepository): Response
    {
        $place = new Place();
        $placeForm = $this->createForm(PlaceType::class, $place);

        $placeForm->handleRequest($request);

        if ($placeForm->isSubmitted() && $placeForm->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                throw $this->createNotFoundException('User not found.');
            }

            $place = $placeForm->getData();
            $place->setOrganizer($user);

            $em->persist($place);
            $em->flush();

            $this->addFlash('success', 'Le lieu a bien été ajouté!');

            return $this->redirectToRoute('place_list');
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

        $places = [];
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $places = $placeRepository->findByFilters($searchForm->getData());
        } else {
            $places = $placeRepository->findAll();
        }

        return $this->render('place/list.html.twig', [
            'placeForm' => $placeForm->createView(),
            'places' => $places,
            'searchForm' => $searchForm
        ]);
    }

    #[Route('/place/{id}/edit', name: 'place_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Place $place): Response
    {
        $placeForm = $this->createForm(PlaceType::class, $place);

        $placeForm->handleRequest($request);

        if ($placeForm->isSubmitted() && $placeForm->isValid()) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Place updated successfully!'
            );

            return $this->redirectToRoute('place_list');
        }

        return $this->render('place/edit.html.twig', [
            'placeForm' => $placeForm->createView(),
        ]);
    }

    #[Route('/admin/place/{id}/delete', name: 'place_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, Place $place): Response
    {
        $placeForm = $this->createFormBuilder()->getForm();

        $placeForm->handleRequest($request);

        if ($placeForm->isSubmitted() && $placeForm->isValid()) {
            $entityManager->remove($place);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le lieu a bien été supprimé !'
            );

            return $this->redirectToRoute('place_list');
        }

        return $this->render('place/delete.html.twig', [
            'placeForm' => $placeForm->createView(),
            'place' => $place,
        ]);
    }
}
