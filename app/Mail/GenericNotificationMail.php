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
    public $batchId; // thêm batch_id

    public function __construct($title, $messageHtml, $messageText = null, $batchId = null)
    {
        $this->title = $title;
        $this->messageHtml = $messageHtml;
        $this->messageText = $messageText ?? trim(preg_replace('/\s+/', ' ', strip_tags($messageHtml)));
        $this->batchId = $batchId;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('admin.emails.generic_notification', [
                'title' => $this->title,
                'messageHtml' => $this->messageHtml,
            ])
            ->text('admin.emails.generic_notification_plain', [
                'title' => $this->title,
                'messageText' => $this->messageText,
            ]);
    }
}
