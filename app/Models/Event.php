<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'club_id',
        'name',
        'description',
        'start_time',
        'end_time',
        'location',
        'max_participants',
        'is_public',
        'status',
        'created_by',
        'approval_by',        // GIỮ NGUYÊN
        'media_id',
        'budget_estimated',
        'budget_current',
        'budget_used',
        'deleted_by',         // ĐÃ CÓ TRONG DB
        'delete_reason',
    ];
    public function getPosterUrlAttribute()
{
    return $this->poster ? asset('storage/' . $this->poster) : null;
}

    protected $casts = [
        'start_time'        => 'datetime',
        'end_time'          => 'datetime',
        'is_public'         => 'boolean',
        'max_participants'  => 'integer',
        'budget_estimated'  => 'decimal:2',
        'budget_current'    => 'decimal:2',
        'budget_used'       => 'decimal:2',
        'status'            => 'string',
    ];

    protected $dates = ['deleted_at'];

    // =================================================================
    // RELATIONSHIPS – GIỮ NGUYÊN TÊN CŨ ĐỂ KHÔNG LỖI
    // =================================================================

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class)->withDefault(['name' => 'Không có CLB']);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault([
            'name' => 'Không xác định',
            'email' => '',
        ]);
    }

    // GIỮ NGUYÊN TÊN approvalBy – KHÔNG ĐỔI NỮA!
    public function approvalBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_by')->withDefault([
            'name' => 'Chưa duyệt',
            'email' => '',
        ]);
    }

    // CHỈ SỬA DUY NHẤT 1 CHỖ: deleteBy → deletedBy
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withDefault([
            'name' => 'Hệ thống',
            'email' => '',
        ]);
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class)->withDefault();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FundTransaction::class, 'event_id');
    }

    // =================================================================
    // SCOPES
    // =================================================================
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopeUpcoming(Builder $query, int $days = 7): Builder
    {
        return $query->whereBetween('start_time', [now(), now()->addDays($days)]);
    }

    public function scopeOfClub(Builder $query, $clubId): Builder
    {
        return $query->where('club_id', $clubId);
    }

    // =================================================================
    // ACCESSORS
    // =================================================================
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'  => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Bị từ chối',
            default    => ucfirst($this->status),
        };
    }

    public function getBudgetEstimatedFormattedAttribute(): string
    {
        return $this->budget_estimated
            ? number_format($this->budget_estimated, 0, ',', '.') . ' VNĐ'
            : '—';
    }
    public function getHasParticipantLimitAttribute(): bool
    {
        return !is_null($this->max_participants);
    }
    public function getRegisteredCountAttribute(): int
    {
        return $this->registrations()->count();
    }
    public function getIsFullAttribute(): bool
    {
        return $this->has_participant_limit && $this->registered_count >= $this->max_participants;
    }
}