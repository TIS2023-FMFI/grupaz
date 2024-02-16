<?php

namespace App\Controller\Admin;

use App\Form\DeleteType;
use App\Repository\CarGroupRepository;
use App\Entity\Log;
use App\Repository\HistoryCarGroupRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

#[Route('/admin/{_locale<%app.supported_locales%>}/delete', name:'app_delete_')]
class DeleteController extends AbstractController
{
    #[Route('/car',name: 'car')]
    public function car(Request $request, HistoryCarGroupRepository $historyCarGroupRepository, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(DeleteType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $start = $form->get('start')->getData();
            $end = $form->get('end')->getData();
            $end = $end->modify('23:59:59');
            $result = $historyCarGroupRepository->deleteByFormData($start, $end);
            if ($result === 0) {
                $this->addFlash(
                    'warning',
                    new TranslatableMessage('delete.car.no_data_in_interval', [
                        '%start%' => $start->format('Y-m-d H:i'),
                        '%end%' => $end->format('Y-m-d H:i')
                    ])
                );
            }
            else {
                $this->addFlash(
                    'success',
                    new TranslatableMessage('delete.confirmation', [
                        '%start%' => $start->format('Y-m-d'),
                        '%end%' => $end->format('Y-m-d')
                    ])
                );
            }
            $log = new Log();
            $log->setTime(new DateTimeImmutable());
            $log->setLog("Vymazanie dát od: {$start->format('d.m.Y')}, do: {$end->format('d.m.Y')}");
            $log->setAdminId((int)$this->getUser()->getId());
            $log->setObjectId(NULL);
            $log->setObjectClass('DeleteController');

            $managerRegistry->getManager()->persist($log);
            $managerRegistry->getManager()->flush();
        }
        return $this->render('admin/delete.html.twig', [
            'form' => $form,
        ]);
    }


}
