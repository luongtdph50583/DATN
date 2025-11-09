<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubRequestMemberUpdate extends Model
{
    protected $fillable = [
        'club_request_update_id',
        'user_id',
        'role',
    ];

    public function requestUpdate()
    {
        return $this->belongsTo(ClubRequestUpdate::class, 'club_request_update_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
