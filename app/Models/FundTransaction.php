<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundTransaction extends Model
{
 protected $fillable = [
    'club_id', 'type', 'amount', 'collected_amount', 'description', 'category',
    'status', 'created_by', 'approved_by', 'receipt', 'event_id',
];


    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    // → CHỈ GIỮ 2 DÒNG NÀY THÔI
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    // → XÓA HẾT 2 HÀM createdBy() và approvedBy() Ở DƯỚI ĐI!

    // Scopes và Accessors giữ nguyên...
    public function scopeIncome($query) { return $query->where('type', 'income'); }
    public function scopeExpense($query) { return $query->where('type', 'expense'); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
    public function scopePending($query) { return $query->where('status', 'pending'); }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', '.') . ' VND';
    }

    public function getStatusBadgeAttribute()
    {
        return ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'][$this->status] ?? 'secondary';
    }
}