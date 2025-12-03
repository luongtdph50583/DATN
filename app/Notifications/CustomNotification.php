<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CustomNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $contentHtml;
    public string $contentText;
    public string $batchId;
    public string $actionType;
    public ?string $relatedModel;
    public ?int $relatedId;
    public int $senderId; // ⭐ thêm senderId

    public function __construct(
        string $title,
        string $contentHtml,
        string $contentText,
        string $batchId,
        int $senderId,                     // ⭐ thêm
        string $actionType = 'general',
        ?int $relatedId = null,
        ?string $relatedModel = null
    ) {
        $this->title = $title;
        $this->contentHtml = $contentHtml;
        $this->contentText = $contentText;
        $this->batchId = $batchId;

        $this->senderId = $senderId;       // ⭐ lưu sender
        $this->actionType = $actionType;
        $this->relatedId = $relatedId;
        $this->relatedModel = $relatedModel;
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

            // ⭐ Ghi lại người gửi
            'sender_id' => $this->senderId,

            // ⭐ Trường phân loại
            'action_type' => $this->actionType,
            'related_model' => $this->relatedModel,
            'related_id' => $this->relatedId,
        ];
    }

    public function afterCommit()
    {
        return true;
    }
}
