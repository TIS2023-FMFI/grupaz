<?php

namespace App\Controller;

use App\Entity\CarGroup;
use App\Form\ScanCarFormType;
use App\Repository\CarRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_car_')]
class CarController extends AbstractController
{
    #[Route('/{_locale<%app.supported_locales%>}/group-{id}/car', name: 'view')]
    public function view(CarGroup $carGroup, CarRepository $carRepository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        if ($carGroup->getStatus() != 2){
            if ($carGroup->getStatus() < 3){
                $carGroup->setStatus(0);
            }
            $this->addFlash('danger', 'entity.carGroup.no_access');
            return $this->redirectToRoute('app_index');
        }
        $form = $this->createForm(ScanCarFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vis = $form->get('vis')->getData();
            $car = $carRepository->findOneByVisInGroup($carGroup->getId(), $vis);

            if ($car === null) {
                $this->addFlash('warning', 'entity.car.wrong_car');
                $carRepository->unloadAllCarInGroup($carGroup->getId());
                $carGroup->setStatus(0);
                $managerRegistry->getManager()->flush();
                return $this->redirectToRoute('app_index');
            }

            $car->setStatus(1);
            $managerRegistry->getManager()->flush();

            $count = $carRepository->countAllLoaded($carGroup->getId()) + $carRepository->countAllDamaged($carGroup->getId());
            if ($count === $carGroup->getCars()->count()) {
                $this->addFlash('success', 'entity.car.all_loaded');
                $carGroup->setStatus(3);
                $managerRegistry->getManager()->flush();
                return $this->redirectToRoute('app_index');
            }

            return $this->redirectToRoute('app_car_view', ['id' => $carGroup->getId(),]);
        }

        return $this->render('app/car/car_view.html.twig', [
            'form' => $form,
            'carGroup' => $carGroup,
        ]);
    }
}