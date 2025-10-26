<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'user_id', 'gender', 'date_of_birth', 'address',
        'course', 'major', 'citizen_id', 'issued_date',
        'issued_place', 'ethnicity', 'phone', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function clubMembers()
    {
        return $this->hasMany(ClubMember::class, 'member_id');
    }

    // 🔹 Nếu muốn lấy nhanh danh sách CLB của member
    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_members', 'member_id', 'club_id')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }
}
