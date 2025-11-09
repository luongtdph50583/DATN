<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Event extends Model
{
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
    'approval_by',
    'media_id',
    'budget_estimated',
    'budget_current',
    'budget_used',
];

   protected $casts = [
        'start_time' => 'datetime', // SỬA: Ép kiểu cho start_time
        'end_time' => 'datetime',   // THÊM: Ép kiểu cho end_time
        'is_public' => 'boolean',   // THÊM: Ép kiểu cho is_public
        'budget' => 'decimal:2',    // THÊM: Ép kiểu decimal (10, 2)
        'status' => 'string',
        'max_participants' => 'integer',
    ];

    /**
     * Quan hệ: Sự kiện thuộc về một CLB
     */
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class)->withDefault([
            'name' => 'Không có CLB',
        ]);
    }

    /**
     * Quan hệ: Người tạo sự kiện
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault([
            'name' => 'Không xác định',
            'email' => '',
        ]);
    }

    /**
     * Quan hệ: Người duyệt sự kiện
     */
    public function approvalBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_by')->withDefault([
            'name' => 'Chưa duyệt',
            'email' => '',
        ]);
    }

    /**
     * Quan hệ: Hình ảnh/video sự kiện (nếu có bảng media)
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class)->withDefault();
    }

    /**
     * Quan hệ: Danh sách đăng ký tham gia sự kiện
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    // =================================================================
    // SCOPES – Tiện ích lọc nhanh
    // =================================================================

    /**
     * Lọc sự kiện đang chờ duyệt
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Lọc sự kiện đã duyệt
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * Lọc sự kiện bị từ chối
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Lọc sự kiện công khai
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    /**
     * Lọc sự kiện sắp diễn ra (từ hiện tại đến 7 ngày tới)
     */
    public function scopeUpcoming(Builder $query, int $days = 7): Builder
    {
        return $query->whereBetween('start_time', [
            now(),
            now()->addDays($days),
        ]);
    }

    /**
     * Lọc sự kiện theo CLB
     */
    public function scopeOfClub(Builder $query, $clubId): Builder
    {
        return $query->where('club_id', $clubId);
    }

    // =================================================================
    // ACCESSORS – Hiển thị đẹp hơn
    // =================================================================

    /**
     * Trả về trạng thái dưới dạng tiếng Việt
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Chờ duyệt',
            'approved' => 'Đã duyệt',
            'rejected' => 'Bị từ chối',
            default => ucfirst($this->status),
        };
    }

    /**
     * Trả về ngân sách định dạng tiền tệ
     */
    public function getBudgetFormattedAttribute(): string
    {
        return $this->budget ? number_format($this->budget, 0, ',', '.') . ' VNĐ' : '—';
    }

    /**
     * Kiểm tra sự kiện có giới hạn người tham gia không
     */
    public function getHasParticipantLimitAttribute(): bool
    {
        return !is_null($this->max_participants);
    }

    /**
     * Trả về số người đã đăng ký (nếu có quan hệ registrations)
     */
    public function getRegisteredCountAttribute(): int
    {
        return $this->registrations()->count();
    }

    /**
     * Kiểm tra còn chỗ không
     */
    public function getIsFullAttribute(): bool
    {
        if (!$this->has_participant_limit) {
            return false;
        }

        return $this->registered_count >= $this->max_participants;
    }
    public function transactions()
{
    return $this->hasMany(FundTransaction::class, 'event_id');
}
  public function clubMembers($clubId)
    {
        // Lấy danh sách thành viên của CLB
        $members = ClubMember::where('club_id', $clubId)
            ->with('user') // quan hệ với bảng users
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->user->id,
                    'name' => $m->user->name,
                    'email' => $m->user->email,
                ];
            });

        return response()->json($members);
    }

}