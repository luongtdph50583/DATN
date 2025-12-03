<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\SentEmail;
use App\Mail\GenericNotificationMail;
use App\Notifications\ClientNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\DatabaseNotification;

class SendNotificationJobClient implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;
    public string $title;
    public string $contentHtml;
    public string $contentText;
    public string $sendVia;
    public string $batchId;
    public bool $force;
    public array $context;

    public function __construct(
        int $userId,
        string $title,
        string $content,
        string $sendVia,
        string $batchId,
        bool $force = false,
        ?string $contentText = null,
        array $context = []
    ) {
        $this->userId = $userId;
        $this->title = $title;
        $this->contentHtml = $content;
        $this->sendVia = $sendVia;
        $this->batchId = $batchId;
        $this->force = $force;
        $this->contentText = $contentText ?? $this->fallbackPlain($content);
        $this->context = $context;
    }

    protected function fallbackPlain(string $html): string
    {
        return trim(preg_replace('/\s+/', ' ', strip_tags($html)));
    }

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user) {
            Log::warning("SendNotificationJobClient: User {$this->userId} not found.");
            return;
        }

        Log::info('SendNotificationJobClient start', [
            'user_id' => $user->id,
            'batchId' => $this->batchId,
            'sendVia' => $this->sendVia,
            'force' => $this->force,
        ]);

        // === NEW: Extract sender_id ===
        $senderId = $this->context['sender_id'] ?? null;

        // Extract context
        $actionType = $this->context['action_type'] ?? 'general';
        $relatedId = $this->context['related_id'] ?? null;
        $relatedModel = $this->context['related_model'] ?? null;

        // ===================================================================
        // IN-APP NOTIFICATION
        // ===================================================================
        if (in_array($this->sendVia, ['database', 'both'])) {

            $notification = DatabaseNotification::where('batch_id', $this->batchId)
                ->where('notifiable_id', $user->id)
                ->latest()
                ->first();

            $shouldSendInApp =
                $this->force ||
                !$notification ||
                ($notification->data['status'] ?? 'sent') === 'failed';

            if ($shouldSendInApp) {
                try {
                    // --- SEND CLIENT NOTIFICATION ---
                    $user->notify(new ClientNotification(
                        $this->title,
                        $this->contentHtml,
                        $this->contentText,
                        $this->batchId,
                        $actionType,
                        $relatedId,
                        $relatedModel,
                        $senderId // <-- NEW
                    ));

                    // --- UPDATE STATUS ---
                    if ($notification) {
                        $notification->data = array_merge($notification->data, [
                            'status' => 'sent',
                            'sender_id' => $senderId
                        ]);
                        $notification->save();
                    } else {
                        sleep(1);
                        $latest = DatabaseNotification::where('batch_id', $this->batchId)
                            ->where('notifiable_id', $user->id)
                            ->latest()
                            ->first();

                        if ($latest) {
                            $latest->data = array_merge($latest->data, [
                                'status' => 'sent',
                                'sender_id' => $senderId
                            ]);
                            $latest->save();
                        }
                    }

                } catch (\Exception $e) {
                    Log::error("Error sending Client In-App notification to user {$user->id}: " . $e->getMessage());

                    if ($notification) {
                        $notification->data = array_merge($notification->data, [
                            'status' => 'failed'
                        ]);
                        $notification->save();
                    }
                }
            }
        }

        // ===================================================================
        // EMAIL NOTIFICATION
        // ===================================================================
        if (in_array($this->sendVia, ['mail', 'both'])) {

            $emailRecord = SentEmail::where('batch_id', $this->batchId)
                ->where('user_id', $user->id)
                ->first();

            $shouldSendMail =
                $this->force ||
                !$emailRecord ||
                ($emailRecord->status === 'failed');

            if ($shouldSendMail) {
                try {
                    Mail::to($user->email)->send(
                        new GenericNotificationMail(
                            $this->title,
                            $this->contentHtml,
                            $this->contentText,
                            $this->batchId
                        )
                    );

                    SentEmail::updateOrCreate(
                        ['batch_id' => $this->batchId, 'user_id' => $user->id],
                        [
                            'title' => $this->title,
                            'content' => $this->contentHtml,
                            'status' => 'sent',
                            'sender_id' => $senderId // <-- NEW (optional nếu bạn muốn)
                        ]
                    );

                } catch (\Exception $e) {

                    SentEmail::updateOrCreate(
                        ['batch_id' => $this->batchId, 'user_id' => $user->id],
                        [
                            'title' => $this->title,
                            'content' => $this->contentHtml,
                            'status' => 'failed',
                            'sender_id' => $senderId
                        ]
                    );
                }
            }
        }
    }
}
