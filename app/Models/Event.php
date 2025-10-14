<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'club_id', 'name', 'description', 'event_date', 'location', 'status', 'created_by'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'status' => 'string',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }
}