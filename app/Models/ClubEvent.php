<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClubEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'club_id', 'created_by', 'title', 'description', 'type',
        'start_time', 'end_time', 'location', 'max_participants', 'is_published'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_published' => 'boolean'
    ];

    public function club() { return $this->belongsTo(Club::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function registrations() { return $this->hasMany(EventRegistration::class, 'event_id'); }
    public function attendances() { return $this->hasMany(EventAttendance::class, 'event_id'); }
}