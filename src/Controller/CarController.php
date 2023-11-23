<?php

namespace App\Controller;

use App\Entity\CarGroup;
use App\Form\EndScanFormType;
use App\Form\ScanCarFormType;
use App\Service\CarManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_car_')]
class CarController extends AbstractController
{
//    private RequestStack $requestStack;
//
//    public function __construct(RequestStack $requestStack)
//    {
//        $this->requestStack = $requestStack;
//    }

    #[Route('/{_locale<%app.supported_locales%>}/group-{id}/car', name: 'view')]
    public function view(CarGroup $carGroup, CarManager $carManager, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $scanForm = $this->createForm(ScanCarFormType::class);
        $endForm = $this->createForm(EndScanFormType::class);
        $scanForm->handleRequest($request);
        $endForm->handleRequest($request);
//        $this->requestStack->getSession()->set('verifyed',true);
        if ($scanForm->isSubmitted() && $scanForm->isValid()) {
            $vis = $scanForm->get('vis')->getData();
            $loaded = $carManager->loadCar($carGroup, $vis, $managerRegistry);
            if (!$loaded) {
                $carManager->unloadCars($carGroup, $managerRegistry);
                return $this->redirectToRoute('app_index');
            }
        }
        if ($endForm->isSubmitted()) {
            if ($carManager->allIsLoaded($carGroup)) {
                return $this->redirectToRoute('app_index');
            }
            $carManager->unloadCars($carGroup, $managerRegistry);
            return $this->redirectToRoute('app_index');
        }

        return $this->render('app/car/car_view.html.twig', [
            'scanForm' => $scanForm,
            'endForm' => $endForm,
            'carGroup' => $carGroup,
        ]);
    }
}