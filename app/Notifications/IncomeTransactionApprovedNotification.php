<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\FundTransaction;

class IncomeTransactionApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $transactionId;
    public $clubId;
    public $clubName;
    public $amount;
    public $description;
    public $category;
    public $approvedByName;
    public $approvedAt;

    public function __construct(FundTransaction $transaction)
    {
        $this->transactionId   = $transaction->id;
        $this->clubId          = $transaction->club_id;
        $this->clubName        = $transaction->club?->name ?? 'Câu lạc bộ';
        $this->amount          = $transaction->amount;
        $this->description     = $transaction->description;
        $this->category        = $transaction->category;

        // Lấy tên người duyệt an toàn tuyệt đối
        $this->approvedByName = 'Hệ thống';
        if ($transaction->relationLoaded('approver') && $transaction->approver) {
            $this->approvedByName = $transaction->approver->name;
        } elseif ($transaction->approved_by) {
            $this->approvedByName = \App\Models\User::find($transaction->approved_by)?->name ?? 'Hệ thống';
        }

        $this->approvedAt = $transaction->approved_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i');
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toArray($notifiable)
    {
        return [
            'type'           => 'income_approved',
            'transaction_id' => $this->transactionId,
            'club_id'        => $this->clubId,
            'club_name'      => $this->clubName,
            'amount'         => $this->amount,
            'description'    => $this->description,
            'message'        => "Khoản thu đã được duyệt",
            // DÙNG URL TRỰC TIẾP – KHÔNG DÙNG route() ĐỂ TRÁNH LỖI KHI QUEUE
            'url'            => url("/clubs/{$this->clubId}/funds"),
        ];
    }

    public function toMail($notifiable)
    {
        $amount = number_format($this->amount, 0, ',', '.') . ' ₫';

        return (new MailMessage)
                    ->subject("[$this->clubName] Khoản thu đã được duyệt")
                    ->greeting("Chào {$notifiable->name}")
                    ->line("Khoản thu của bạn đã được duyệt thành công!")
                    ->line("**Số tiền:** {$amount}")
                    ->line("**Nội dung:** {$this->description}")
                    ->when($this->category, fn($m) => $m->line("**Danh mục:** {$this->category}"))
                    ->line("**Người duyệt:** {$this->approvedByName}")
                    ->line("**Thời gian:** {$this->approvedAt}")
                    // DÙNG url() THAY route() – CHẠY 100% KHI QUEUE
                    ->action('Xem quỹ CLB', url("/clubs/{$this->clubId}/funds"))
                    ->line('Cảm ơn bạn đã đóng góp!')
                    ->salutation("Trân trọng,\n{$this->clubName}");
    }
}