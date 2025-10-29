<?php

namespace App\Observers;

use Illuminate\Notifications\DatabaseNotification;

class DatabaseNotificationObserver
{
    public function saving(DatabaseNotification $notification)
    {
        \Log::info('Observer saving called', ['data' => $notification->data]);

        if (isset($notification->data['batch_id'])) {
            $notification->batch_id = $notification->data['batch_id'];
        }

        if (isset($notification->data['status'])) {
            $notification->status = $notification->data['status'];
        }
    }
}

