<?php

namespace App\Controller;

use App\Entity\CarGroup;
use App\Form\CarGroupType;
use App\Form\EndFormType;
use App\Repository\CarRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'app_car_group_')]
class CarGroupController extends AbstractController
{
    #[Route('/{_locale<%app.supported_locales%>}/group-{id}', name: 'view')]
    public function view(CarGroup $carGroup, Request $request, ManagerRegistry $managerRegistry, CarRepository $carRepository): Response
    {
        if ($carGroup->getStatus() != 1) {
            if ($carGroup->getStatus() < 3){
                $carGroup->setStatus(0);
            }
            $this->addFlash('danger', 'entity.carGroup.no_access');
            return $this->redirectToRoute('app_index');
        }
        $end = $this->createForm(EndFormType::class);
        $end->handleRequest($request);
        $form = $this->createForm(CarGroupType::class, $carGroup);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $carGroup->setStatus(2);
            $carRepository->unloadAllCarInGroup($carGroup->getId());
            $managerRegistry->getManager()->flush();
            return $this->redirectToRoute('app_car_view', [
                'id' => $carGroup->getId(),
            ]);
        }
        if ($end->isSubmitted() && $end->isValid()) {
            $carRepository->unloadAllCarInGroup($carGroup->getId());
            $carGroup->setStatus(0);
            $managerRegistry->getManager()->flush();
            return $this->redirectToRoute('app_index_no_locale');
        }
        return $this->render('app/car_group/view.html.twig', [
            'form' => $form,
            'end' => $end,
            'carGroup' => $carGroup,
        ]);
    }
}
