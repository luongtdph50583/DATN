<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubRequest extends Model
{
    protected $table = 'club_requests';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'field',
        'status',
    ];

    // Mối quan hệ: mỗi yêu cầu thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
