<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubRequestUpdate extends Model
{
    protected $fillable = [
        'club_id',
        'name',
        'slogan',
        'description',
        'field',
        'member_limit',
        'manager_id',
        'advisor_id',
        'email',
        'phone',
        'logo',
        'rules',
        'location',
        'advisor_status'
    ];

    // Quan hệ với CLB
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    // Quan hệ với các member đề xuất
    public function memberUpdates()
    {
        return $this->hasMany(ClubRequestMemberUpdate::class, 'club_request_update_id');
    }
    public function proposer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
