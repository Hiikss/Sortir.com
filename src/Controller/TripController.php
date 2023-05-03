<?php

namespace App\Controller;

use APP\Entity\Trip;
use App\Form\TripFormType;
use App\Repository\StateRepository;
use App\Repository\TripRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trip', name: 'trip_')]
class TripController extends AbstractController
{

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager, StateRepository $stateRepository): Response
    {
        $trip = new Trip();
        $form = $this->createForm(TripFormType::class, $trip);

        $states = $stateRepository->findAll();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('save')->isClicked()) {
                $trip->setState($states[0]);
            } else if ($form->get('publish')->isClicked()) {
                $trip->setState($states[1]);
            }

            $user = $this->getUser();
            $trip->setOrganizer($user);
            $place = $form->get('place')->getData();
            $trip->setPlace($place);

            $entityManager->persist($trip);
            $entityManager->persist($place);
            $entityManager->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('trip/create.html.twig', [
            'tripForm' => $form
        ]);
    }


    #[Route('/cancel/{id}', name: 'cancel')]
    public function cancel(Request $request, int $id, TripRepository $tripRepository, StateRepository $stateRepository, EntityManagerInterface $entityManager): Response {

        $trip = $tripRepository->find($id);

        $states = $stateRepository->findAll();

        if($trip && ($this->getUser()==$trip->getOrganizer() || $this->isGranted('ROLE_ADMIN')) && ($trip->getState()==$states[1] || $trip->getState()==$states[2])
            && $trip->getStartDateTime()>new DateTime('now') /*&& !is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"))*/) {

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
                'tripForm' => $tripForm
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

    #[Route('/details/{id}', name: 'details')]
    public function details(int $id, TripRepository $tripRepository): Response {
        
        $trip = $tripRepository->find($id);

        if($trip) {
            return $this->render('trip/details.html.twig', [
                'trip' => $trip
            ]);
        }
        
        return $this->redirectToRoute('app_main');
    }

    #[Route('/publish/{id}', name: 'publish')]
    public function publish(int $id, TripRepository $tripRepository, StateRepository $stateRepository, EntityManagerInterface $entityManager): Response {
        
        $trip = $tripRepository->find($id);

        $states = $stateRepository->findAll();

        $user = $this->getUser();
        if($trip && $trip->getState()==$states[0] && $trip->getOrganizer()==$user && $trip->getLimitEntryDate()>new DateTime('now')) {
                
            $trip->setState($states[1]);
            
            $entityManager->persist($trip);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_main');
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id, TripRepository $tripRepository, StateRepository $stateRepository, Request $request): Response {
        
        $trip = $tripRepository->find($id);

        $states = $stateRepository->findAll();

        $user = $this->getUser();
        if($trip && $trip->getState()==$states[0] && $trip->getOrganizer()==$user) {

            $tripForm = $this->createForm(TripFormType::class, $trip);

            $tripForm->handleRequest($request);

            if ($tripForm->isSubmitted() && $tripForm->isValid()) {
                if ($tripForm->get('save')->isClicked()) {
                    //$trip->setState($states[0]);
                } else if ($tripForm->get('publish')->isClicked()) {
                    //$trip->setState($states[1]);
                } else if($tripForm->get('delete')->isClicked()) {
                    $tripRepository->remove($trip, true);
                }
                return $this->redirectToRoute('app_main');
            }
            
            return $this->render('trip/edit.html.twig', [
                'tripForm' => $tripForm,
                'trip' => $trip
            ]);
        }

    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(int $id, TripRepository $tripRepository, StateRepository $stateRepository): Response
    {
        $trip = $tripRepository->find($id);

        $user = $this->getUser();

        $states = $stateRepository->findAll();
        
        if($trip && $trip->getState()==$states[0] && $trip->getOrganizer()==$user) {
            $tripRepository->remove($trip, true);
        }

        return $this->redirectToRoute('app_main');
    }
}



