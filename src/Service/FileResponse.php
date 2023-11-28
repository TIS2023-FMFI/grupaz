<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;

class FileResponse
{
    public static function get(string $content, string $filename, string $filetype): Response
    {
        $response = new Response($content);
        $response->headers->set('Content-Type', $filetype);
        $response->headers->set('Content-Disposition', 'attachment; filename="'. $filename .'"');
        return $response;
    }
}