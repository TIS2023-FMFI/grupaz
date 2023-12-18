<?php

namespace App\Controller;

use App\Entity\CarGroup;
use App\Form\EndFormType;
use App\Form\ScanCarFormType;
use App\Repository\CarRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

#[Route(name: 'app_car_')]
class CarController extends AbstractController
{
    #[Route('/{_locale<%app.supported_locales%>}/group-{id}/car', name: 'view')]
    public function view(CarGroup $carGroup, CarRepository $carRepository, Request $request, ManagerRegistry $managerRegistry): Response
    {
        if ($carGroup->getStatus() != 2) {
            if ($carGroup->getStatus() < 3) {
                $carGroup->setStatus(0);
            }
            $this->addFlash('danger', 'entity.carGroup.no_access');
            return $this->redirectToRoute('app_index');
        }
        $last = $request->query->get('last');
        $end = $this->createForm(EndFormType::class);
        $end->handleRequest($request);
        $form = $this->createForm(ScanCarFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vis = $form->get('vis')->getData();
            $car = $carRepository->findOneByVisInGroup($carGroup->getId(), $vis);

            if ($car === null) {
                if ($last != null) {
                    if (!str_contains($last, $vis)) {
                        $last .= '; ';
                        $last .= $vis;
                    }
                } else {
                    $last = $vis;
                }
            }
            if ($last != null) {
                $this->addFlash('warning', new TranslatableMessage('entity.car.wrong_car', [
                    '%carVis%' => $last
                ]));
            }

            if ($car != null) {
                $car->setStatus(1);
                $managerRegistry->getManager()->flush();
            }
            $count = $carRepository->countAllLoaded($carGroup->getId()) + $carRepository->countAllDamaged($carGroup->getId());
            if ($last === null && $count === $carGroup->getCars()->count()) {
                $this->addFlash('success', 'entity.car.all_loaded');
                $carGroup->setStatus(3);
                $managerRegistry->getManager()->flush();
                return $this->redirectToRoute('app_index');
            }

            return $this->redirectToRoute('app_car_view', ['id' => $carGroup->getId(), 'last' => $last]);
        }
        if ($end->isSubmitted() && $end->isValid()) {
            $carRepository->unloadAllCarInGroup($carGroup->getId());
            $carGroup->setStatus(0);
            $managerRegistry->getManager()->flush();
            return $this->redirectToRoute('app_index_no_locale');
        }

        return $this->render('app/car/car_view.html.twig', [
            'form' => $form,
            'end' => $end,
            'carGroup' => $carGroup,
        ]);
    }
}