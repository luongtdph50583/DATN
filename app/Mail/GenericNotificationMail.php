<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $messageHtml;
    public $messageText;
    public $batchId;
    public $senderName; // Thêm tên người gửi

    public function __construct($title, $messageHtml, $messageText = null, $batchId = null, $senderName = null)
    {
        $this->title = $title;
        $this->messageHtml = $messageHtml;
        $this->messageText = $messageText ?? trim(preg_replace('/\s+/', ' ', strip_tags($messageHtml)));
        $this->batchId = $batchId;
        $this->senderName = $senderName ?? 'System'; // Mặc định là System nếu không có người gửi
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('admin.emails.generic_notification', [
                'title' => $this->title,
                'messageHtml' => $this->messageHtml,
                'senderName' => $this->senderName, // Truyền vào view
            ])
            ->text('admin.emails.generic_notification_plain', [
                'title' => $this->title,
                'messageText' => $this->messageText,
                'senderName' => $this->senderName, // Truyền vào text view
            ]);
    }
}