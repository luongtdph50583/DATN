<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClubLeaveRequest extends Model
{
    use HasFactory;

    protected $table = 'club_leave_requests';

    protected $fillable = [
        'club_id',
        'user_id',
        'status',
        'handled_by',
        'requested_at',
        'handled_at',
        'reason',
        'note',
    ];

    protected $dates = [
        'requested_at',
        'handled_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'requested_at' => 'datetime',
        'handled_at' => 'datetime',
    ];


    // 🔹 Quan hệ tới bảng CLB
    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    // 🔹 Người gửi yêu cầu (User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔹 Hồ sơ thành viên (Member)
    public function member()
    {
        return $this->hasOne(Member::class, 'user_id', 'user_id');
    }

    // 🔹 Người xử lý yêu cầu (Admin hoặc quản lý)
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
