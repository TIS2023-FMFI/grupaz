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
        $form = $this->createForm(ScanCarFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vis = $form->get('vis')->getData();
            $car = $carRepository->findOneByVisInGroup($carGroup->getId(), $vis);

            if ($car === null){
                $this->addFlash('warning', 'Scanned wrong car.');
                $this->unloadCars($carGroup);
                $carGroup->setStatus(0);
                $managerRegistry->getManager()->flush();
                return $this->redirectToRoute('app_index');
            }

            $car->setStatus(1);
            $managerRegistry->getManager()->flush();

            $count = $carRepository->countAllLoaded($carGroup->getId());
            dump($count);
            if ($count === $carGroup->getCars()->count()){
                $this->addFlash('success', 'All cars have been loaded.');
                $carGroup->setStatus(3);
                $managerRegistry->getManager()->flush();
                return $this->redirectToRoute('app_index');
            }
        }

        return $this->render('app/car/car_view.html.twig', [
            'form' => $form,
            'carGroup' => $carGroup,
        ]);
    }

    public function unloadCars(CarGroup $carGroup): void
    {
        foreach ($carGroup->getCars() as $car){
            $car->setStatus(0);
        }
    }

}