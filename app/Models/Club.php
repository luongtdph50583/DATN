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
        'field',
        'email',
        'phone',
        'logo',
        'member_limit',
        'status',
        'manager_id',
    ];

    // Chủ nhiệm CLB (User có role = club_manager)
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Thành viên CLB (qua bảng trung gian)
    public function members()
    {
        return $this->belongsToMany(Member::class, 'club_members', 'club_id', 'member_id')
                    ->withPivot(['role', 'joined_at'])
                    ->withTimestamps();
    }

    // Bài viết CLB
    public function posts()
    {
        return $this->hasMany(Post::class, 'club_id');
    }

    // Sự kiện CLB
    public function events()
    {
        return $this->hasMany(Event::class, 'club_id');
    }

    // Giao dịch quỹ
    public function fundTransactions()
    {
        return $this->hasMany(FundTransaction::class, 'club_id');
    }

    // Yêu cầu tham gia CLB
    public function joinRequests()
    {
        return $this->hasMany(ClubJoinRequest::class, 'club_id');
    }
}
