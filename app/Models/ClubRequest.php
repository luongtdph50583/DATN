<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubRequest extends Model
{
    use HasFactory;

    protected $table = 'club_requests';

    protected $fillable = [
        'user_id',
        'name',
        'slogan',
        'description',
        'field',
        'plan',
        'plan_file',
        'purpose',
        'email',
        'phone',
        'logo',
        'rule',
        'member_limit',

        // Ban chủ nhiệm
        'club_manager_id',
        'deputy_manager_id',
        'secretary_id',
        'treasurer_id',
        'event_manager_id',
        'communication_id',

        // Giảng viên (nếu có)
        // 'advisor_id',
        // 'advisor_status',

        // Xử lý
        'status',
        'handled_by',
        'note',
        'rejection_reason',

        // PDF
        'pdf_file',
        'pdf_generated_at',

        // Timestamps
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'pdf_generated_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Người gửi yêu cầu tạo CLB (creator)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias cho user() - để dễ hiểu hơn
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Admin xử lý yêu cầu
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Chủ nhiệm CLB
     */
    public function clubManager()
    {
        return $this->belongsTo(User::class, 'club_manager_id');
    }

    /**
     * Phó chủ nhiệm CLB
     */
    public function deputyManager()
    {
        return $this->belongsTo(User::class, 'deputy_manager_id');
    }

    /**
     * Thư ký CLB
     */
    public function secretary()
    {
        return $this->belongsTo(User::class, 'secretary_id');
    }

    /**
     * Thủ quỹ CLB
     */
    public function treasurer()
    {
        return $this->belongsTo(User::class, 'treasurer_id');
    }

    /**
     * Quản lý sự kiện CLB
     */
    public function eventManager()
    {
        return $this->belongsTo(User::class, 'event_manager_id');
    }

    /**
     * Phụ trách truyền thông CLB
     */
    public function communication()
    {
        return $this->belongsTo(User::class, 'communication_id');
    }

    /**
     * Giảng viên đỡ đầu (liên kết với user)
     */
    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    /**
     * Thành viên ban đầu (many-to-many qua pivot table)
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'club_request_members', 'club_request_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Danh sách xác nhận từ thành viên
     */
    public function confirmations()
    {
        return $this->hasMany(ClubRequestConfirmation::class, 'club_request_id');
    }

    /**
     * Danh sách phê duyệt từ admin
     */
    public function approvals()
    {
        return $this->hasMany(ClubRequestApproval::class, 'club_request_id');
    }

    /**
     * CLB được tạo ra từ đơn này (nếu đã phê duyệt)
     */
    public function club()
    {
        return $this->hasOne(Club::class, 'club_request_id');
    }

    /**
     * Thành viên/ban quản lý đề xuất trong yêu cầu CLB (backward compatible)
     */
    public function clubRequestMembers()
    {
        return $this->hasMany(ClubRequestMember::class, 'club_request_id');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Kiểm tra tất cả thành viên đã xác nhận chưa
     */
    public function allMembersConfirmed()
    {
        $totalMembers = $this->members()->count();

        if ($totalMembers === 0) {
            return false;
        }

        $confirmedMembers = $this->confirmations()
            ->where('status', true)
            ->count();

        return $totalMembers === $confirmedMembers;
    }

    /**
     * Kiểm tra admin đã phê duyệt chưa
     */
    public function isApprovedByAdmin()
    {
        return $this->approvals()
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Kiểm tra admin đã từ chối chưa
     */
    public function isRejectedByAdmin()
    {
        return $this->approvals()
            ->where('status', 'rejected')
            ->exists();
    }

    /**
     * Lấy danh sách ban chủ nhiệm dạng array
     */
    public function getManagementTeam()
    {
        return [
            'club_manager' => $this->clubManager,
            'deputy_manager' => $this->deputyManager,
            'secretary' => $this->secretary,
            'treasurer' => $this->treasurer,
            'event_manager' => $this->eventManager,
            'communication' => $this->communication,
        ];
    }

    /**
     * Lấy phần trăm xác nhận
     */
    public function getConfirmationPercentage()
    {
        $total = $this->members()->count();

        if ($total === 0) {
            return 0;
        }

        $confirmed = $this->confirmations()
            ->where('status', true)
            ->count();

        return round(($confirmed / $total) * 100, 2);
    }

    /**
     * Lấy số lượng thành viên đã xác nhận
     */
    public function getConfirmedMembersCount()
    {
        return $this->confirmations()
            ->where('status', true)
            ->count();
    }

    /**
     * Lấy số lượng thành viên chưa xác nhận
     */
    public function getPendingMembersCount()
    {
        return $this->confirmations()
            ->where('status', false)
            ->count();
    }

    /**
     * Kiểm tra user có phải thành viên của đơn này không
     */
    public function hasMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    /**
     * Kiểm tra user có phải ban chủ nhiệm không
     */
    public function isManagementMember($userId)
    {
        return in_array($userId, [
            $this->club_manager_id,
            $this->deputy_manager_id,
            $this->secretary_id,
            $this->treasurer_id,
            $this->event_manager_id,
            $this->communication_id,
        ]);
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Chỉ lấy đơn đang chờ xử lý
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Chỉ lấy đơn đã xác nhận (chờ admin duyệt)
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope: Chỉ lấy đơn đã được phê duyệt
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Chỉ lấy đơn bị từ chối
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope: Lấy đơn của user cụ thể
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Lấy đơn mới nhất
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ==================== ACCESSORS ====================

    /**
     * Accessor: Lấy trạng thái dạng text tiếng Việt
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'approved' => 'Đã phê duyệt',
            'rejected' => 'Đã từ chối',
        ];

        return $statuses[$this->status] ?? 'Không xác định';
    }

    /**
     * Accessor: Lấy màu badge cho trạng thái
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'warning',
            'confirmed' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Accessor: Kiểm tra có PDF chưa
     */
    public function getHasPdfAttribute()
    {
        return !empty($this->pdf_file) && \Storage::disk('public')->exists($this->pdf_file);
    }

    /**
     * Accessor: Lấy URL logo
     */
    public function getLogoUrlAttribute()
    {
        if (empty($this->logo)) {
            return asset('images/default-club-logo.png');
        }

        return asset('storage/' . $this->logo);
    }
}
