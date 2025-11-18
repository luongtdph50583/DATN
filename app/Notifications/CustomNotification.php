<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CustomNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $contentHtml;
    public string $contentText;
    public string $batchId;
    public array $context;

    public function __construct(string $title, string $contentHtml, string $contentText, string $batchId, array $context = [])
    {
        $this->title = $title;
        $this->contentHtml = $contentHtml;
        $this->contentText = $contentText;
        $this->batchId = $batchId;
        $this->context = $context;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->contentText,
            'message_html' => $this->contentHtml,
            'status' => 'sent',
            'batch_id' => $this->batchId,
            'context' => $this->context,
        ];
    }


    // Ghi batch_id vào cột riêng
    public function afterCommit()
    {
        return true; // nếu dùng queue, commit xong mới lưu
    }
}
