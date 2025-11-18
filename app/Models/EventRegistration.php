<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $fillable = ['event_id', 'user_id', 'status', 'registered_at'];

    public function event() { return $this->belongsTo(ClubEvent::class); }
    public function user() { return $this->belongsTo(User::class); }
}