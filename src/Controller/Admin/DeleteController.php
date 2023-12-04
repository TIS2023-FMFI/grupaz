<?php

namespace App\Controller\Admin;

use App\Form\DeleteType;
use App\Form\ExportType;
use App\Repository\CarRepository;
use App\Serializer\CarNormalizer;
use App\Service\FileResponse;
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
    /**
     * @throws ExceptionInterface
     */
    #[Route('/car',name: 'car')]
    public function car(Request $request, CarRepository $carRepository): Response
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
            return $this->redirectToRoute('admin', ['routeName' => 'app_delete_car']);
        }
        return $this->render('admin/delete.html.twig', [
            'form' => $form,
        ]);
    }


}
