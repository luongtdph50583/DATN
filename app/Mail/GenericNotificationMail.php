<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $messageContent;
    public $batchId; // thêm batch_id

    public function __construct($title, $messageContent, $batchId = null)
    {
        $this->title = $title;
        $this->messageContent = $messageContent;
        $this->batchId = $batchId;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('admin.emails.generic_notification')
            ->with([
                'title' => $this->title,
                'messageContent' => $this->messageContent,
                'batchId' => $this->batchId, // truyền vào view nếu cần
            ]);
    }
}
