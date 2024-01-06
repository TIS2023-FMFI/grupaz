<?php

namespace App\Controller\Admin;

use App\Form\UploadType;
use App\Service\FileUploader;
use App\Entity\Log;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

#[Route('/admin/{_locale<%app.supported_locales%>}/import', name:'app_import_')]
class ImportController extends AbstractController
{
    #[Route('/car', name: 'car')]
    public function car(Request $request, FileUploader $fileUploader, ManagerRegistry $managerRegistry): Response
    {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form->get('uploadedFile')->getData();
            try {
                $importedCars = $fileUploader->upload($uploadedFile);
                if (0 < $importedCars) {
                    $log = new Log();
                    $log->setTime(new \DateTimeImmutable());
                    $log->setLog("Vykonaný import súboru: $uploadedFile");
                    $log->setAdminId((int)$this->getUser()->getId());
                    $log->setObjectId(NULL);
                    $log->setObjectClass('ImportController');
                    $managerRegistry->getManager()->persist($log);
                    $managerRegistry->getManager()->flush();
                    $this->addFlash(
                        'success',
                        new TranslatableMessage('import.car.success', [
                            '%importedCars%' => $importedCars
                        ])
                    );
                    return $this->redirectToRoute('admin');
                }
                else {
                    $this->addFlash(
                        'danger',
                        'import.car.no_cars_in_file'
                    );
                }
            }
            catch (\Throwable $exception) {
                $this->addFlash(
                    'danger',
                    'import.car.wrong_file_keys'
                );
            }
        }
        return $this->render('admin/import.html.twig', [
            'form' => $form,
        ]);
    }
}
