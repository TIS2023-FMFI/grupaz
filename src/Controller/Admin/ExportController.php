<?php

namespace App\Controller\Admin;

use App\Form\ExportType;
use App\Repository\CarRepository;
use App\Serializer\CarNormalizer;
use App\Service\FileResponse;
use DateTimeImmutable;
use App\Entity\Log;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Translation\TranslatableMessage;

#[Route('/admin/{_locale<%app.supported_locales%>}/export', name:'app_export_')]
class ExportController extends AbstractController
{
    #[Route('/car',name: 'car')]
    public function car(Request $request, CarRepository $carRepository, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(ExportType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $start = $form->get('start')->getData();
            $end = $form->get('end')->getData();
            $end = $end->modify('23:59:59');
            $result = $carRepository->findByFormData($start, $end);
            if (empty($result)) {
                $this->addFlash(
                    'warning',
                    new TranslatableMessage('export.car.no_data_in_interval', [
                        '%start%' => $start->format('Y-m-d H:i'),
                        '%end%' => $end->format('Y-m-d H:i')
                    ])
                );
                return $this->redirectToRoute('admin', ['routeName' => 'app_export_car']);
            }
            $log = new Log();
            $log->setTime(new \DateTimeImmutable());
            $log->setLog('Vykonaný export dát od: ' . $start->format('d.m.Y') . ' do: ' . $end->format('d.m.Y'));
            $log->setAdminId((int)$this->getUser()->getId());
            $log->setObjectId(NULL);
            $log->setObjectClass('ExportController');

            $managerRegistry->getManager()->persist($log);
            $managerRegistry->getManager()->flush();
            $serializer = new Serializer([new CarNormalizer()], [new CsvEncoder()]);
            $content = $serializer->serialize($result, 'csv');
            return FileResponse::get($content, sprintf('cars_%s_%s.csv', $start->format('Y-m-d'), $end->format('Y-m-d')),'text/csv');
        }
        return $this->render('admin/export.html.twig', [
            'form' => $form,
        ]);
    }


}
