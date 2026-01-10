<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class ClubLeaveRequestNotification extends Notification
{
    public function __construct(public $leaveRequest) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type'    => 'leave_club',
            'club_id' => $this->leaveRequest->club_id,
            'user_id' => $this->leaveRequest->user_id,

            // 👉 Message rõ ràng
            'message' => $this->leaveRequest->user->name 
                        . ' đã rời khỏi CLB ' 
                        . $this->leaveRequest->club->name,

            'reason'  => $this->leaveRequest->reason,
        ];
    }
}
