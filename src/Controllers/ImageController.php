<?php

namespace App\Controllers;

/**
 * Secure Image Serving Controller (Protects storage outside DocRoot)
 */
class ImageController extends Controller
{
    public function serve(string $filename): void
    {
        // Prevent path traversal
        $filename = basename($filename);
        $filePath = dirname(__DIR__, 2) . '/storage/uploads/' . $filename;

        if (!file_exists($filePath) || is_dir($filePath)) {
            http_response_code(404);
            header('Content-Type: text/plain');
            echo '404 Image Not Found';
            exit;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($filePath);

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=86400');
        readfile($filePath);
        exit;
    }
}
