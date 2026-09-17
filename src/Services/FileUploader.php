<?php

namespace App\Services;

/**
 * Secure File Uploader for Ticket and Resolution Images
 */
class FileUploader
{
    private string $uploadDir;
    private array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    private int $maxBytes = 5242880; // 5 MB

    public function __construct(?string $uploadDir = null)
    {
        $this->uploadDir = $uploadDir ?? dirname(__DIR__, 2) . '/storage/uploads';
        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0777, true);
        }
    }

    /**
     * Upload an image from $_FILES array
     * @return string|null Relative path to stored image or null on failure
     */
    public function upload(array $file): ?string
    {
        if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > $this->maxBytes) {
            throw new \RuntimeException('ขนาดไฟล์รูปภาพเกินกำหนด (สูงสุด 5MB)');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $this->allowedMimes, true)) {
            throw new \RuntimeException('ชนิดไฟล์ไม่ถูกต้อง อนุญาตเฉพาะ JPG, PNG และ WEBP');
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            default      => 'bin'
        };

        $filename = 'img_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $targetPath = $this->uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'storage/uploads/' . $filename;
        }

        return null;
    }
}
