<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'field',
        'status',
        'leader_id', // cột người phụ trách
    ];

    // 👤 Người phụ trách (chủ nhiệm CLB)
    public function leader()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // 🔗 Các yêu cầu tham gia CLB
    public function joinRequests()
    {
        return $this->hasMany(ClubJoinRequest::class);
    }

    // 🧑‍🤝‍🧑 Thành viên CLB (nếu bạn có bảng trung gian)
    public function members()
    {
        return $this->hasMany(ClubMember::class);
    }
}
