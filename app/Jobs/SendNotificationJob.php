<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\SentEmail;
use App\Mail\GenericNotificationMail;
use App\Notifications\CustomNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\DatabaseNotification;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;
    public string $title;
    public string $content;
    public string $sendVia;
    public string $batchId;
    public bool $force;

    public function __construct(int $userId, string $title, string $content, string $sendVia, string $batchId, bool $force = false)
    {
        $this->userId = $userId;
        $this->title = $title;
        $this->content = $content;
        $this->sendVia = $sendVia;
        $this->batchId = $batchId;
        $this->force = $force;
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            Log::warning("SendNotificationJob: User {$this->userId} not found.");
            return;
        }

        Log::info('SendNotificationJob start', [
            'user_id' => $user->id,
            'batchId' => $this->batchId,
            'sendVia' => $this->sendVia,
            'force' => $this->force,
        ]);

        // =====================
        // In-App Notification
        // =====================
        if (in_array($this->sendVia, ['database', 'both'])) {
            $notification = DatabaseNotification::where('batch_id', $this->batchId)
                ->where('notifiable_id', $user->id)
                ->latest()
                ->first();

            $shouldSendInApp = $this->force || !$notification || ($notification->data['status'] ?? 'sent') === 'failed';

            Log::info("In-App check", [
                'has_record' => (bool) $notification,
                'status' => $notification->data['status'] ?? null,
                'should_send' => $shouldSendInApp,
            ]);

            if ($shouldSendInApp) {
                try {
                    $user->notify(new CustomNotification($this->title, $this->content, $this->batchId));

                    // Cập nhật record cũ hoặc lấy bản ghi mới nhất vừa tạo
                    if ($notification) {
                        $notification->data = array_merge($notification->data, ['status' => 'sent']);
                        $notification->save();
                    } else {
                        sleep(1); // chờ lưu
                        $latest = DatabaseNotification::where('batch_id', $this->batchId)
                            ->where('notifiable_id', $user->id)
                            ->latest()
                            ->first();
                        if ($latest) {
                            $latest->data = array_merge($latest->data, ['status' => 'sent']);
                            $latest->save();
                        }
                    }

                    Log::info("In-App notification sent to user {$user->id}");
                } catch (\Exception $e) {
                    Log::error("Error sending In-App notification to user {$user->id}: " . $e->getMessage());

                    if ($notification) {
                        $notification->data = array_merge($notification->data, ['status' => 'failed']);
                        $notification->save();
                    }
                }
            }
        }

        // =====================
        // Email Notification
        // =====================
        if (in_array($this->sendVia, ['mail', 'both'])) {
            $emailRecord = SentEmail::where('batch_id', $this->batchId)
                ->where('user_id', $user->id)
                ->first();

            $shouldSendMail = $this->force || !$emailRecord || ($emailRecord->status === 'failed');

            Log::info("Email check", [
                'has_record' => (bool) $emailRecord,
                'status' => $emailRecord->status ?? null,
                'should_send' => $shouldSendMail,
            ]);

            if ($shouldSendMail) {
                try {
                    Mail::to($user->email)->send(new GenericNotificationMail($this->title, $this->content));

                    SentEmail::updateOrCreate(
                        ['batch_id' => $this->batchId, 'user_id' => $user->id],
                        [
                            'title' => $this->title,
                            'content' => $this->content,
                            'status' => 'sent',
                        ]
                    );

                    Log::info("Email sent to user {$user->id}");
                } catch (\Exception $e) {
                    Log::error("Error sending email to user {$user->id}: " . $e->getMessage());

                    SentEmail::updateOrCreate(
                        ['batch_id' => $this->batchId, 'user_id' => $user->id],
                        [
                            'title' => $this->title,
                            'content' => $this->content,
                            'status' => 'failed',
                        ]
                    );
                }
            }
        }
    }
}
