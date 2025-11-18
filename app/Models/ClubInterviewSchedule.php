<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubInterviewSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'interviewer_id',
        'club_id',
        'scheduled_at',
        'location',
        'status',
        'note',
        'interview_result',
        'score',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'integer',
    ];

    /**
     * Yêu cầu tham gia CLB
     */
    public function joinRequest()
    {
        return $this->belongsTo(ClubJoinRequest::class, 'request_id');
    }

    /**
     * Người phỏng vấn
     */
    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    /**
     * CLB
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
