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
    public function list(Request $request, EntityManagerInterface $em, PlaceRepository $placeRepository): Response
    {
        $place = new Place();
        $placeForm = $this->createForm(PlaceType::class, $place);

        $placeForm->handleRequest($request);

        if ($placeForm->isSubmitted() && $placeForm->isValid()) {

            $em->persist($place);
            $em->flush();

            $this->addFlash(
                'success',
                'Le lieu a bien été ajouté!'
            );

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

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $places = $placeRepository->findByFilters($searchForm->getData());
        } else {
            $places = $placeRepository->findAll();
        }

        return $this->render('place/list.html.twig', [
            'placeForm' => $placeForm,
            'places' => $places,
            'searchForm' => $searchForm
        ]);
    }

    #[Route('/place/edit/{id}', name: 'place_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $em, PlaceRepository $placeRepository): Response
    {
        $place = $placeRepository->find($id);

        $placeForm = $this->createForm(PlaceType::class, $place);

        $placeForm->handleRequest($request);

        if ($placeForm->isSubmitted() && $placeForm->isValid()) {
            $place= $placeForm->getData();

            $em->persist($place);
            $em->flush();

            $this->addFlash(
                'success',
                'Le lieu a bien été modifié !'
            );

            return $this->redirectToRoute('place_list');
        }

        return $this->render('place/edit.html.twig', [
            'placeForm' => $placeForm
        ]);
    }

    #[Route('/admin/place/delete/{id}', name: 'place_delete')]
    public function delete(int $id, Request $request, PlaceRepository $placeRepository): Response
    {
        $place = $placeRepository->find($id);

        $form = $this->createFormBuilder()->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $placeRepository->remove($place, true);

            $this->addFlash(
                'success',
                'Le lieu a bien été supprimé !'
            );

            return $this->redirectToRoute('place_list');
        }

        return $this->render('place/delete.html.twig', [
            'form' => $form,
            'place' => $place,
        ]);
    }
}
