<?php
namespace App\Notifications;

use App\Models\ClubJoinRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterviewScheduledNotification extends Notification
{
    use Queueable;

    protected $request;

    public function __construct(ClubJoinRequest $request)
    {
        $this->request = $request;
    }

    // Chỉ gửi database (in-app notification)
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Thông báo lịch phỏng vấn',
            'club_name' => $this->request->club->name,
            'scheduled_at' => $this->request->interview_scheduled_at?->format('d/m/Y H:i'),
            'location' => $this->request->interview_location,
            'interviewer' => optional($this->request->interviewer)->name,
            'note' => $this->request->interview_note,
            'message' => "CLB {$this->request->club->name} đã lên lịch phỏng vấn cho bạn.",
            'request_id' => $this->request->id,
        ];
    }
}
