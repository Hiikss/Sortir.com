<?php

namespace App\Controller;

use App\Form\HomeType;
use App\Repository\TripRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(TripRepository $tripRepository, Request $request): Response
    {
        $homeForm = $this->createForm(HomeType::class);

        $homeForm->handleRequest($request);

        if($homeForm->isSubmitted() && $homeForm->isValid()) {
            $campus = $homeForm->get('campus')->getData();
            $searchzone = $homeForm->get('searchzone')->getData();
            $startDate = $homeForm->get('startDate')->getData();
            $endDate = $homeForm->get('endDate')->getData();
            $organizerTrips = $homeForm->get('organizerTrips')->getData();
            $registeredTrips = $homeForm->get('registeredTrips')->getData();
            $notRegisteredTrips = $homeForm->get('notRegisteredTrips')->getData();
            $pastTrips = $homeForm->get('pastTrips')->getData();
            $trips = $tripRepository->findByFilters($this->getUser(), $campus, $searchzone, $startDate, $endDate, $organizerTrips, $registeredTrips, $notRegisteredTrips, $pastTrips);
        }
        else {
            $homeForm->get('organizerTrips')->setData(true);
            $homeForm->get('registeredTrips')->setData(true);
            $homeForm->get('notRegisteredTrips')->setData(true);
            $trips = $tripRepository->findAll();
        }
            
        return $this->render('main/index.html.twig', [
            'homeForm' => $homeForm->createView(),
            'trips' => $trips
        ]);
    }
}
