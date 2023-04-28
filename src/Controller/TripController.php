<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Place;
use APP\Entity\Trip;
use App\Form\PlaceType;
use App\Form\TripFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trip', name: 'trip_')]
class TripController extends AbstractController
{

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trip = new Trip();

        $form = $this->createForm(TripFormType::class, $trip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trip);
            $entityManager->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('trip/create.html.twig', [
            'TripForm' => $form->createView(),
        ]);
    }
}



