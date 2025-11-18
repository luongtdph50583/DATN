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
        'appointed_at',   // ngày bổ nhiệm
        'status',         // trạng thái active/inactive
        'note'            // nếu muốn lưu lý do hay ghi chú
    ];


    public $timestamps = true;

      public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
protected static function boot()
    {
        parent::boot();

        static::deleting(function ($member) {
            if ($member->club && $member->club->manager_id == $member->member_id) {
                $member->club->update(['manager_id' => null]);
            }
        });
    }
    // app/Models/ClubMember.php
    public function memberInfo()
    {
        return $this->belongsTo(Member::class, 'user_id', 'user_id');
    }


}
