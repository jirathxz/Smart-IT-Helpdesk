<?php

namespace App\Services;

use Exception;
use InvalidArgumentException;

class FileUploader
{
    private string $uploadDir;
    private int $maxSizeBytes;
    private array $allowedMimeTypes;

    public function __construct(
        ?string $uploadDir = null,
        int $maxSizeMb = 5,
        array $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif']
    ) {
        $baseDir = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        $this->uploadDir = $uploadDir ?? ($baseDir . '/storage/uploads');
        $this->maxSizeBytes = $maxSizeMb * 1024 * 1024;
        $this->allowedMimeTypes = $allowedMimeTypes;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload and validate image file
     * @param array $file $_FILES['input_name']
     * @return string Relative storage path (e.g. 'storage/uploads/tk_6500_abc.jpg')
     * @throws Exception
     */
    public function upload(array $file): string
    {
        if (!isset($file['error']) || is_array($file['error'])) {
            throw new InvalidArgumentException('ข้อมูลไฟล์อัปโหลดไม่ถูกต้อง');
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new InvalidArgumentException('ไม่พบไฟล์ที่เลือกอัปโหลด');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new InvalidArgumentException('ขนาดไฟล์ใหญ่เกินขีดจำกัดที่ระบบอนุญาต');
            default:
                throw new Exception('เกิดข้อผิดพลาดในการอัปโหลดไฟล์ (Error Code: ' . $file['error'] . ')');
        }

        // Validate File Size
        if ($file['size'] > $this->maxSizeBytes) {
            $mb = $this->maxSizeBytes / (1024 * 1024);
            throw new InvalidArgumentException("ขนาดไฟล์ต้องไม่เกิน {$mb}MB");
        }

        // Validate MIME type strictly via Fileinfo (inspects binary magic bytes)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedMimeTypes, true)) {
            throw new InvalidArgumentException("ประเภทไฟล์ไม่ได้รับอนุญาต (อนุญาตเฉพาะ JPG, PNG, WEBP)");
        }

        // Determine extension safely from MIME type
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];
        $ext = $extensions[$mimeType] ?? 'jpg';

        // Generate safe randomized filename
        $fileName = sprintf(
            'tk_%s_%s.%s',
            date('Ymd_His'),
            bin2hex(random_bytes(6)),
            $ext
        );

        $destination = rtrim($this->uploadDir, '/\\') . DIRECTORY_SEPARATOR . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('ไม่สามารถบันทึกไฟล์ไปยังโฟลเดอร์ปลายทางได้');
        }

        // Return relative path from project root
        return 'storage/uploads/' . $fileName;
    }

    public function delete(string $relativePath): bool
    {
        $baseDir = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        $fullPath = $baseDir . '/' . ltrim($relativePath, '/');
        if (file_exists($fullPath) && is_file($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
