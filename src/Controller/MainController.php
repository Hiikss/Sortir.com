<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Repository\TripRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(TripRepository $tripRepository, Request $request): Response
    {
        $homeForm = $this->createFormBuilder()
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name'
            ])
            ->add('searchzone', TextType::class, [
                'required' => false,
                'label' => 'Le nom de la sortie contient',
                'attr' => [
                    'placeholder' => 'rechercher'
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
            ])
            ->add('registeredTrips', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit/e',
                'required' => false
            ])
            ->add('notRegisteredTrips', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit/e',
                'required' => false
            ])
            ->add('pastTrips', CheckboxType::class, [
                'label' => 'Sorties passées',
                'required' => false
            ])->getForm();

        $homeForm->handleRequest($request);

        if ($homeForm->isSubmitted() && $homeForm->isValid()) {
            $trips = $tripRepository->findByFilters($this->getUser(), $homeForm->getData());
        } else {

            // vous n'avez pas besoin de faire ça pour attribuer une valeur par défaut aux champs
            // on peut le faire directement en configuration le form.
            // Regardez la doc symfony sur le CheckBoxType par exemple et cherchez comment attribuer une valeur
            // par défaut

            $homeForm->get('campus')->setData($this->getUser()->getCampus());
            $homeForm->get('organizerTrips')->setData(true);
            $homeForm->get('registeredTrips')->setData(true);
            $homeForm->get('notRegisteredTrips')->setData(true);
            $trips = $tripRepository->findByFilters($this->getUser(), ['campus' => $this->getUser()->getCampus(), 'searchzone' => null, 'startDate' => null, 'endDate' => null,
             'organizerTrips' => true, 'registeredTrips' => true, 'notRegisteredTrips' => true, 'pastTrips' => false]);
        }

        return $this->render('main/index.html.twig', [
            'homeForm' => $homeForm->createView(),
            'trips' => $trips
        ]);
    }
}
