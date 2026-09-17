<?php

namespace App\Services;

use App\Repositories\CommentRepository;
use App\Repositories\TicketRepository;
use InvalidArgumentException;
use Exception;

class CommentService
{
    private CommentRepository $commentRepo;
    private TicketRepository $ticketRepo;
    private FileUploader $uploader;

    public function __construct(
        ?CommentRepository $commentRepo = null,
        ?TicketRepository $ticketRepo = null,
        ?FileUploader $uploader = null
    ) {
        $this->commentRepo = $commentRepo ?? new CommentRepository();
        $this->ticketRepo = $ticketRepo ?? new TicketRepository();
        $this->uploader = $uploader ?? new FileUploader();
    }

    public function addComment(int $ticketId, int $userId, string $body, ?array $file = null): array
    {
        $body = trim($body);
        if (empty($body) && (!$file || empty($file['tmp_name']))) {
            throw new InvalidArgumentException('กรุณาระบุข้อความหรือแนบรูปภาพ');
        }

        $ticket = $this->ticketRepo->find($ticketId);
        if (!$ticket) {
            throw new InvalidArgumentException('ไม่พบตั๋วแจ้งซ่อม');
        }

        $imagePath = null;
        if ($file && isset($file['tmp_name']) && !empty($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploader->upload($file);
        }

        $commentId = $this->commentRepo->create($ticketId, $userId, $body ?: 'ส่งรูปภาพประกอบ', $imagePath);

        return [
            'id'         => $commentId,
            'ticket_id'  => $ticketId,
            'user_id'    => $userId,
            'body'       => $body,
            'image_path' => $imagePath,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }
}
