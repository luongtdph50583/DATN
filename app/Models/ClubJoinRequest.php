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
        'status',
        'note',                    // Ghi chú xử lý
        'handled_by',              // Người xử lý
        'requested_at',            // Thời gian yêu cầu
        'handled_at',              // Thời gian xử lý
        'interview_scheduled_at',  // Thời gian lên lịch phỏng vấn
        'interviewer_id',          // Người phỏng vấn
        'interview_location',      // Địa điểm phỏng vấn
        'interview_note',          // Ghi chú phỏng vấn
        'interview_result',        // Kết quả phỏng vấn
        'interview_completed_at',  // Thời gian hoàn thành phỏng vấn
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'handled_at' => 'datetime',
        'interview_scheduled_at' => 'datetime',
        'interview_completed_at' => 'datetime',
       
    ];

    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Người phỏng vấn
     */
    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    /**
     * Lịch phỏng vấn (nếu có bảng riêng)
     */
    public function interviewSchedules()
    {
        return $this->hasMany(ClubInterviewSchedule::class, 'request_id');
    }

    /**
     * Câu trả lời form tuyển thành viên
     */
    public function formAnswers()
    {
        return $this->hasMany(ClubJoinFormAnswer::class, 'request_id');
    }
}
