<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubMember extends Model
{
    use HasFactory;

    protected $table = 'club_members';

    protected $fillable = [
        'club_id',
        'member_id',
        'role',
        'joined_at',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    // 🔹 Thành viên (bảng members) của CLB
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
