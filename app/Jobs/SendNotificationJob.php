<?php

namespace App\Jobs;

use App\Models\User;
use App\Mail\GenericNotificationMail;
use App\Notifications\CustomNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $title,
        public string $content,
        public string $sendVia
    ) {
    }

    public function handle()
    {
        if ($this->sendVia === 'database' || $this->sendVia === 'both') {
            $this->user->notify(new CustomNotification($this->title, $this->content));
        }

        if ($this->sendVia === 'mail' || $this->sendVia === 'both') {
            Mail::to($this->user->email)->send(new GenericNotificationMail($this->title, $this->content));
        }
    }
}
