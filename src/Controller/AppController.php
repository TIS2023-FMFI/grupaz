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
    public function index(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(FilterCarGroupType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $carGroup = $form->get('gid')->getData();
            if ($carGroup->getStatus() >= 3){
                $this->addFlash('warning', 'entity.carGroup.closed');
                return $this->redirectToRoute('app_index');
            }
            $carGroup->setStatus(1);
            $managerRegistry->getManager()->flush();
            return $this->redirectToRoute('app_car_group_view', [
                'id' => $carGroup->getId(),
            ]);
        }
        return $this->render('app/index.html.twig', [
            'form' => $form,
        ]);
    }
}
