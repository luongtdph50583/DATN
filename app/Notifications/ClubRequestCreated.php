<?php

// app/Notifications/ClubRequestCreated.php
namespace App\Notifications;

use App\Models\ClubRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class ClubRequestCreated extends Notification
{
    use Queueable;

    protected $clubRequest;

    public function __construct(ClubRequest $clubRequest)
    {
        $this->clubRequest = $clubRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'mail']; // Gửi qua database và email
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Xác nhận tham gia CLB: ' . $this->clubRequest->name)
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line('Bạn được mời tham gia CLB **' . $this->clubRequest->name . '**.')
            ->line('Người tạo: ' . $this->clubRequest->creator->name)
            ->line('Lĩnh vực: ' . $this->clubRequest->field)
            ->action('Xem chi tiết và xác nhận', route('club_requests.show', $this->clubRequest))
            ->line('Vui lòng xác nhận tham gia để hoàn tất quá trình thành lập CLB.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'club_request_id' => $this->clubRequest->id,
            'club_name' => $this->clubRequest->name,
            'creator_name' => $this->clubRequest->creator->name,
            'message' => 'Bạn được mời tham gia CLB ' . $this->clubRequest->name,
        ];
    }
}
