<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClubPlan extends Model
{
    protected $fillable = [
        'club_id', 'title', 'description', 'start_date', 'end_date',
        'budget', 'status', 'created_by', 'approved_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'budget'     => 'decimal:2',
    ];

    // Quan hệ: Kế hoạch thuộc về 1 CLB
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    // Người tạo kế hoạch
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Người duyệt kế hoạch
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Danh sách nhiệm vụ con
    public function tasks(): HasMany
    {
        return $this->hasMany(PlanTask::class, 'plan_id');
    }

    // Scope: Lọc kế hoạch chờ duyệt
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope: Lọc kế hoạch đã duyệt
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Trả về nhãn trạng thái đẹp
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'Nháp',
            'pending'   => 'Chờ duyệt',
            'approved'  => 'Đã duyệt',
            'rejected'  => 'Từ chối',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Hủy',
            default     => 'Không xác định',
        };
    }
}