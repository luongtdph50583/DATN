<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClientNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $contentHtml;
    public string $contentText;
    public string $batchId;
    public string $actionType;
    public ?string $relatedModel;
    public ?int $relatedId;
    public ?int $senderId; // ⭐ thêm vào

    public function __construct(
        string $title,
        string $contentHtml,
        string $contentText,
        string $batchId,
        string $actionType = 'general',
        ?int $relatedId = null,
        ?string $relatedModel = null,
        ?int $senderId = null // ⭐ thêm vào
    ) {
        $this->title = $title;
        $this->contentHtml = $contentHtml;
        $this->contentText = $contentText;
        $this->batchId = $batchId;

        $this->actionType = $actionType;
        $this->relatedId = $relatedId;
        $this->relatedModel = $relatedModel;

        $this->senderId = $senderId; // ⭐ save sender
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

            'action_type' => $this->actionType,
            'related_model' => $this->relatedModel,
            'related_id' => $this->relatedId,

            // ⭐ người gửi
            'sender_id' => $this->senderId,
        ];
    }

    public function afterCommit()
    {
        return true;
    }
}
