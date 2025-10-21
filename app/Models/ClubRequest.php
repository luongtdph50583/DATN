<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClubRequest extends Model
{
    use HasFactory;

    protected $table = 'club_requests';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'field',
        'status',
    ];

    // Mỗi yêu cầu thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
