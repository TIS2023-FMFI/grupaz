<?php

namespace App\Controller;

use App\Entity\CarGroup;
use App\Form\EndFormType;
use App\Form\FilterCarGroupType;
use App\Repository\CarRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    #[Route('/', name: 'app_index_no_locale')]
    public function indexNoLocale(): Response
    {
        //choose language
        return $this->render('app/index_no_locale.html.twig', [
        ]);
    }

    #[Route('/{_locale<%app.supported_locales%>}/', name: 'app_index')]
    public function index(Request $request, ManagerRegistry $managerRegistry, CarRepository $carRepository): Response
    {
        $form = $this->createForm(FilterCarGroupType::class);
        $form->handleRequest($request);
        $end = $this->createForm(EndFormType::class);
        $end->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $carGroup = $form->get('gid')->getData();
            if ($carGroup->getStatus() >= CarGroup::STATUS_ALL_SCANNED){
                $this->addFlash('warning', 'entity.carGroup.closed');
                return $this->redirectToRoute('app_index');
            }
            $carGroup->setStatus(CarGroup::STATUS_START);
            $carRepository->unloadAllCarInGroup($carGroup->getId());
            $managerRegistry->getManager()->flush();
            return $this->redirectToRoute('app_car_group_view', [
                'id' => $carGroup->getId(),
            ]);
        }
        if ($end->isSubmitted() && $end->isValid()) {
            $managerRegistry->getManager()->flush();
            return $this->redirectToRoute('app_index_no_locale');
        }
        return $this->render('app/index.html.twig', [
            'form' => $form,
            'end' => $end,
        ]);
    }
    #[Route('/{_locale<%app.supported_locales%>}/success', name: 'success')]
    public function success(): Response
    {
        return $this->render('app/success.html.twig');
    }
}
