<?php

namespace App\Controller;

use APP\Entity\Trip;
use App\Form\TripFormType;
use App\Repository\StateRepository;
use App\Repository\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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

         //$request->request->getClicked();

         $trip = $form->getData();
         //$trip = $TripRepository->trip($form);

         $entityManager->persist($trip);
         $entityManager->flush();

         return $this->redirectToRoute('app_main');
     }

     return $this->render('trip/create.html.twig', [
         'TripForm' => $form->createView(),
     ]);
    }

    #[Route('/cancel/{id}', name: 'cancel')]
    public function cancel(Request $request, int $id, TripRepository $tripRepository, StateRepository $stateRepository, EntityManagerInterface $entityManager): Response {

        $trip = $tripRepository->find($id);
        
        $states = $stateRepository->findAll();

        if($this->getUser()==$trip->getOrganizer() && $trip->getState()==$states[1]) {
            
            $tripForm = $this->createFormBuilder()
            ->add('reason', TextareaType::class, [
                'label' => 'Motif'
            ])->getForm();

            $tripForm->handleRequest($request);

            if ($tripForm->isSubmitted() && $tripForm->isValid()) {
                
                $trip->setState($states[5]);
                $trip->setCancelReason($tripForm->get('reason')->getData());

                $entityManager->persist($trip);
                $entityManager->flush();
       
                return $this->redirectToRoute('app_main');
            }

            return $this->render('trip/cancel.html.twig', [
                'trip' => $trip,
                'tripForm' => $tripForm->createView()
            ]);
        }

        return $this->redirectToRoute('app_main');
    }
}



