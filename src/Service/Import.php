<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Import {

    public function importFromFile(UploadedFile $file) {
        $file->getContent();

        return null;
    }
}