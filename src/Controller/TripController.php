<?php

namespace App\Controller;

use App\Entity\Campus;
use APP\Entity\Trip;
use App\Form\TripFormType;
use App\Repository\StateRepository;
use App\Repository\TripRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TripController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(TripRepository $tripRepository, Request $request): Response
    {
        $homeForm = $this->createFormBuilder()
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
                'data' => $this->getUser()->getCampus()
            ])
            ->add('searchzone', TextType::class, [
                'required' => false,
                'label' => 'Le nom de la sortie contient',
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('endDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('organizerTrips', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur/trice',
                'required' => false,
                'data' => true
            ])
            ->add('registeredTrips', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false,
                'data' => true
            ])
            ->add('notRegisteredTrips', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false,
                'data' => true
            ])
            ->add('pastTrips', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false
            ])->getForm();

        $homeForm->handleRequest($request);

        if ($homeForm->isSubmitted() && $homeForm->isValid()) {
            $trips = $tripRepository->findByFilters($this->getUser(), $homeForm->getData());
        } else {
            $trips = $tripRepository->findByFilters($this->getUser(), ['campus' => $this->getUser()->getCampus(), 'searchzone' => null, 'startDate' => null, 'endDate' => null,
             'organizerTrips' => true, 'registeredTrips' => true, 'notRegisteredTrips' => true, 'pastTrips' => false]);
        }

        return $this->render('main/index.html.twig', [
            'homeForm' => $homeForm->createView(),
            'trips' => $trips
        ]);
    }

    #[Route('/trip/create', name: 'trip_create')]
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


    #[Route('/trip/cancel/{id}', name: 'trip_cancel')]
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

    #[Route('/trip/register/{id}', name: 'trip_register')]
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

    #[Route('/trip/unregister/{id}', name: 'trip_unregister')]
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

    #[Route('/trip/details/{id}', name: 'trip_details')]
    public function details(int $id, TripRepository $tripRepository): Response {
        
        $trip = $tripRepository->find($id);

        if($trip) {
            return $this->render('trip/details.html.twig', [
                'trip' => $trip
            ]);
        }
        
        return $this->redirectToRoute('app_main');
    }

    #[Route('/trip/publish/{id}', name: 'trip_publish')]
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

    #[Route('/trip/edit/{id}', name: 'trip_edit')]
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

    #[Route('/trip/delete/{id}', name: 'trip_delete')]
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



