<?php

namespace App\Controller\Admin;

use App\Form\DeleteType;
use App\Repository\CarGroupRepository;
use App\Repository\CarRepository;
use App\Entity\Log;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\TranslatableMessage;

#[Route('/admin/{_locale<%app.supported_locales%>}/delete', name:'app_delete_')]
class ApproveController extends AbstractController
{
    #[Route('/car',name: 'car')]
    public function car(Request $request, CarRepository $carRepository, String $gid, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);
        $result = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $carRepository->confirmCarGroup($gid);
            $this->addFlash(
                'success',
                new TranslatableMessage('approve.confirmation', [
                    '%gid%' => $gid,
                ])
            );
            $log->setTime(new \DateTimeImmutable());
            $log->setLog("Potvrdenie grupáže číslo: {$gid}");
            $log->setAdminId((int)$this->getUser()->getId());
            $log->setObjectId(NULL);
            $log->setObjectClass('ApproveController');

            $managerRegistry->getManager()->persist($log);
            $managerRegistry->getManager()->flush();
        }
        return $this->render('admin/dashboard.html.twig', [
            'form' => $form,
            'result' => $result
        ]);
    }


}
