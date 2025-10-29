<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CustomNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $content;
    public string $batchId;

    public function __construct(string $title, string $content, string $batchId)
    {
        $this->title = $title;
        $this->content = $content;
        $this->batchId = $batchId;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->content,
            'status' => 'sent',
            'batch_id' => $this->batchId, // Observer sẽ lấy từ đây
        ];
    }


    // Ghi batch_id vào cột riêng
    public function afterCommit()
    {
        return true; // nếu dùng queue, commit xong mới lưu
    }
}
