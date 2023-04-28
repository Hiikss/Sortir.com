<?php

namespace App\Controller;

use App\Entity\Place;
use App\Form\PlaceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/place', name: 'place_')]
class PlaceController extends AbstractController
{

    #[Route('/create', name: 'create')]
    public function addPlace(Request $request, EntityManagerInterface $entityManager): Response
    {

        $place = new Place();
        $form = $this->createForm(PlaceType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($place);
            $entityManager->flush();

            return $this->redirectToRoute('trip_create');
        }
        return $this->render('place/create.html.twig', [
            'PlaceForm' => $form->createView(),
        ]);
    }
}
