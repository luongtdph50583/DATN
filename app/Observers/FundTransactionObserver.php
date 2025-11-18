<?php

namespace App\Observers;

use App\Models\FundTransaction;
use App\Notifications\IncomeTransactionApprovedNotification;
use Illuminate\Support\Facades\Notification;

class FundTransactionObserver
{
    public function updated(FundTransaction $transaction)
    {
        // Chỉ gửi khi: duyệt khoản thu (status đổi thành approved)
        if ($transaction->isDirty('status') 
            && $transaction->status === 'approved' 
            && $transaction->type === 'income') {

            // Dùng đúng relation bạn đã tạo sẵn → tự động là Collection, không lỗi gì cả
            $recipients = $transaction->club->activeMembers;

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new IncomeTransactionApprovedNotification($transaction));
            }
        }
    }
}