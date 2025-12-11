<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubMemberViolation extends Model
{
    protected $fillable = [
        'club_id',
        'member_id',
        'reported_by',
        'type',
        'description',
        'issued_at',
    ];

    /**
     * CLB liên kết
     */
    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    /**
     * Thành viên bị vi phạm (record trong bảng club_members)
     */
    public function member()
    {
        return $this->belongsTo(ClubMember::class, 'member_id');
    }

    /**
     * Người báo cáo vi phạm (user)
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
