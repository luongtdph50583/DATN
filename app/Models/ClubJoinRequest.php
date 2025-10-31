<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubJoinRequest extends Model
{
    use HasFactory;

    protected $table = 'club_join_requests';

    protected $fillable = [
        'club_id',
        'user_id',
        'reason',
        'status',
        'requested_at'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
