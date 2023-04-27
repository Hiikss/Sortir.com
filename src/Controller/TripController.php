<?php

namespace App\Controller;

use APP\Entity\Trip;
use App\Form\TripFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TripController extends AbstractController
{
    #[Route('/trip/create', name: 'app_create')]
     public function create(Request $request, EntityManagerInterface $entityManager): Response
     {
     $trip = new Trip();
     $form = $this->createForm(TripFormType::class, $trip);

     $form->handleRequest($request);
     if ($form->isSubmitted() && $form->isValid()) {

         //$request->request->getClicked();

         //$trip = $form->getData();
         //$trip = $TripRepository->trip($form);

         $entityManager->persist($trip);
         $entityManager->flush();

         return $this->redirectToRoute('app_main');
     }

     return $this->render('trip/create.html.twig', [
         'TripForm' => $form->createView(),
     ]);
    }
}



