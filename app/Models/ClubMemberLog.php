<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubMemberLog extends Model
{
    protected $fillable = [
        'club_id',
        'member_id',
        'action',
        'performed_by',
        'reason',
    ];

    /**
     * CLB liên kết
     */
    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    /**
     * Thành viên bị tác động (người rời CLB, bị kick, ...)
     */
    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    /**
     * Người thực hiện hành động (admin hoặc null nếu hệ thống)
     */
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
