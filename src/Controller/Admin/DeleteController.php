<?php

namespace App\Controller\Admin;

use App\Form\DeleteType;
use App\Form\ExportType;
use App\Repository\CarRepository;
use App\Serializer\CarNormalizer;
use App\Service\FileResponse;
use App\Entity\Log;
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
class DeleteController extends AbstractController
{
    #[Route('/car',name: 'car')]
    public function car(Request $request, CarRepository $carRepository, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(DeleteType::class, null, [
            'action' => $this->generateUrl('app_delete_car'),
            'method' => 'POST',
            'attr' => [
                'onsubmit' => 'return confirmDelete();',
            ],
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $start = $form->get('start')->getData();
            $end = $form->get('end')->getData();
            $result = $carRepository->deleteByFormData($start, $end);
            if (empty($result)) {
                $this->addFlash(
                    'warning',
                    new TranslatableMessage('delete.car.no_data_in_interval', [
                        '%start%' => $start->format('Y-m-d'),
                        '%end%' => $end->format('Y-m-d')
                    ])
                );
                return $this->redirectToRoute('admin', ['routeName' => 'app_delete_car']);
            }
            $this->addFlash(
                'success',
                new TranslatableMessage('delete.confirmation', [
                    '%start%' => $start->format('Y-m-d'),
                    '%end%' => $end->format('Y-m-d')
                ])
            );
            $log = new Log();
            $log->setTime(new \DateTimeImmutable());
            $log->setLog("Vymazanie dát od: $start->format('d.m.Y'), do: $end->format('d.m.Y)");

            $managerRegistry->getManager()->persist($log);
            $managerRegistry->getManager()->flush();
            $serializer = new Serializer([new CarNormalizer()], [new CsvEncoder()]);
            $content = $serializer->serialize($result, 'csv');
            return FileResponse::get($content, sprintf('cars_%s_%s.csv', $start->format('Y-m-d'), $end->format('Y-m-d')),'text/csv');
        }
        return $this->render('admin/delete.html.twig', [
            'form' => $form,
        ]);
    }


}
