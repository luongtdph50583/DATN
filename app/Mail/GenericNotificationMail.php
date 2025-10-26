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

    public function __construct($title, $messageContent)
    {
        $this->title = $title;
        $this->messageContent = $messageContent;
    }

    public function build()
    {
        return $this->subject($this->title)
            ->view('admin.emails.generic_notification')
            ->with([
                'title' => $this->title,
                'messageContent' => $this->messageContent,
            ]);
    }
}
