<?php

namespace App\Service;

use App\Message\ImportCar;
use App\Message\ImportCars;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;

class FileUploader
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @throws Exception
     */
    public function upload(UploadedFile $file): int
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        //A, E, K .. return [];

        $importCars = [];
        if (isset($sheetData[1])) {
            $headers = $sheetData[1];
            if ($headers['A'] !== 'VIS' || $headers['E'] !== 'Rada' || $headers['K'] !== 'Stanica') {
                throw new \Exception();
            }
            unset($sheetData[1]);
            foreach ($sheetData as $row) {
                //potentially validate not null, "" for "vis" = row A
                $importCars[] = [
                    $row['A'], $row['E'], $row['K']
                ];
            }

            $this->messageBus->dispatch(new ImportCars($importCars));
        }

        return count($importCars);
    }
}