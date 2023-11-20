<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\CarGroup;
use App\Form\CarGroupType;
use App\Form\FilterCarGroupType;
use App\Form\ScanCarFormType;
use App\Repository\CarGroupRepository;
use App\Service\Import;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name:'app_car_')]
class CarController extends AbstractController
{
    #[Route('/{_locale<%app.supported_locales%>}/group-{id}/car', name: 'view')]
    public function view(CarGroup $carGroup, Car $car, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(ScanCarFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
//            $manager = $managerRegistry->getManager();
//            $manager->flush();
            $car ->setStatus(1);
        }

        return $this->render('app/car/car_view.html.twig', [
            'form' => $form,
            'carGroup' => $carGroup,
        ]);
    }
}
