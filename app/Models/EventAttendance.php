<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAttendance extends Model
{
    protected $fillable = ['event_id', 'user_id', 'checked_in_at', 'check_in_method'];

    public function event() { return $this->belongsTo(ClubEvent::class); }
    public function user() { return $this->belongsTo(User::class); }
}