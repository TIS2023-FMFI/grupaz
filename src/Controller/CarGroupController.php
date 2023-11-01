<?php

namespace App\Controller;

use App\Entity\CarGroup;
use App\Form\CarGroupType;
use App\Form\FilterCarGroupType;
use App\Repository\CarGroupRepository;
use App\Service\Import;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name:'app_car_group_')]
class CarGroupController extends AbstractController
{
    #[Route('/{_locale<%app.supported_locales%>}/group-{id}', name: 'view')]
    public function view(CarGroup $carGroup, Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(CarGroupType::class, $carGroup);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $managerRegistry->getManager();
            $manager->flush();
        }

        return $this->render('app/car_group/view.html.twig', [
            'form' => $form,
            'carGroup' => $carGroup,
        ]);
    }
}
