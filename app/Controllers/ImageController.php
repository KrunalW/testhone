<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class ImageController extends BaseController
{
    /**
     * Serve uploaded images from writable directory
     * This is needed because writable folder is not publicly accessible
     */
    public function serve($type, $filename)
    {
        // Log for debugging
        log_message('debug', "ImageController::serve called with type={$type}, filename={$filename}");

        // Validate type
        if (!in_array($type, ['questions', 'options'])) {
            log_message('error', "Invalid image type: {$type}");
            return $this->response->setStatusCode(404, 'Not Found');
        }

        // Sanitize filename to prevent directory traversal
        $filename = basename($filename);

        // Build file path
        $filePath = WRITEPATH . "uploads/{$type}/{$filename}";

        // Check if file exists
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404, 'Image not found');
        }

        // Get mime type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        // Validate it's an image
        if (!str_starts_with($mimeType, 'image/')) {
            return $this->response->setStatusCode(403, 'Invalid file type');
        }

        // Set headers and return image
        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Length', filesize($filePath))
            ->setHeader('Cache-Control', 'public, max-age=31536000') // Cache for 1 year
            ->setBody(file_get_contents($filePath));
    }
}
