<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/city', name: 'admin_city_')]
class CityController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(CityRepository $cityRepository, Request $request, EntityManagerInterface $em): Response
    {
        $city = new City();
        $cityForm = $this->createForm(CityType::class, $city);

        $cityForm->handleRequest($request);

        if ($cityForm->isSubmitted() && $cityForm->isValid()) {
            $em->persist($city);
            $em->flush();

            $this->addFlash(
                'success',
                'La ville a bien été ajoutée !'
            );

            return $this->redirectToRoute('admin_city_list');
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
            $cities = $cityRepository->findByFilters($searchForm->getData());
        }
        else {
            $cities = $cityRepository->findAll();
        }

        return $this->render('city/index.html.twig', [
            'cities' => $cities,
            'cityForm' => $cityForm,
            'searchForm' => $searchForm
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(int $id, CityRepository $cityRepository, Request $request): Response
    {
        $city = $cityRepository->find($id);

        $form = $this->createFormBuilder()->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $city) {
            $cityRepository->remove($city, true);

            $this->addFlash(
                'success',
                'La ville a bien été supprimée !'
            );

            return $this->redirectToRoute('admin_city_list');
        }

        return $this->render('city/delete.html.twig', [
            'city' => $city,
            'form' => $form
        ]);
    }

    #[Route('/modify/{id}', name: 'modify')]
    public function modify(int $id, CityRepository $cityRepository, Request $request, EntityManagerInterface $em): Response
    {
        $city = $cityRepository->find($id);

        $cityForm = $this->createForm(CityType::class, $city);

        $cityForm->handleRequest($request);

        if ($cityForm->isSubmitted() && $cityForm->isValid()) {
            $city= $cityForm->getData();

            $em->persist($city);
            $em->flush();

            $this->addFlash(
                'success',
                'La ville a bien été modifiée !'
            );

            return $this->redirectToRoute('admin_city_list');
        }

        return $this->render('city/modify.html.twig', [
            'cityForm' => $cityForm
        ]);
    }
}
