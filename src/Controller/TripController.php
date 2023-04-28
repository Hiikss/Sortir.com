<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Place;
use APP\Entity\Trip;
use App\Form\PlaceType;
use App\Form\TripFormType;
use App\Repository\StateRepository;
use App\Repository\TripRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    #[Route('/cancel/{id}', name: 'cancel')]
    public function cancel(Request $request, int $id, TripRepository $tripRepository, StateRepository $stateRepository, EntityManagerInterface $entityManager): Response {

        $trip = $tripRepository->find($id);
        
        $states = $stateRepository->findAll();

        if($trip && $this->getUser()==$trip->getOrganizer() && ($trip->getState()==$states[1] || $trip->getState()==$states[2])) {
            
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

    #[Route('/register/{id}', name: 'register')]
    public function register(int $id, TripRepository $tripRepository, StateRepository $stateRepository, EntityManagerInterface $entityManager): Response {
        
        $trip = $tripRepository->find($id);

        $states = $stateRepository->findAll();

        $user = $this->getUser();
        if($trip && $trip->getState()==$states[1] && $trip->getOrganizer()!=$user && !$trip->getRegisteredUsers()->contains($user) 
            && $trip->getRegisteredUsers()->count()<$trip->getMaxRegistrationsNb() && $trip->getLimitEntryDate()>new DateTime('now')) {
                
            $trip->addRegisteredUser($user);

            if($trip->getRegisteredUsers()->count()==$trip->getMaxRegistrationsNb()) {
                $trip->setState($states[2]);
            }
            
            $entityManager->persist($trip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_main');
    }

    #[Route('/unregister/{id}', name: 'unregister')]
    public function unregister(int $id, TripRepository $tripRepository, StateRepository $stateRepository, EntityManagerInterface $entityManager): Response {
        
        $trip = $tripRepository->find($id);

        $states = $stateRepository->findAll();

        $user = $this->getUser();

        if($trip && ($trip->getState()==$states[1] || $trip->getState()==$states[2]) && $trip->getOrganizer()!=$user && $trip->getRegisteredUsers()->contains($user) 
            && $trip->getStartDateTime()>new DateTime('now')) {
                
            $trip->removeRegisteredUser($user);
            if($trip->getLimitEntryDate()>new DateTime('now')) {
                $trip->setState($states[1]);
            }
            
            $entityManager->persist($trip);
                $entityManager->flush();
        }

        return $this->redirectToRoute('app_main');
    }
}



