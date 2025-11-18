<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubJoinFormAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'question_id',
        'answer',
        'answer_json',
    ];

    protected $casts = [
        'answer_json' => 'array',
    ];

    /**
     * Yêu cầu tham gia CLB
     */
    public function joinRequest()
    {
        return $this->belongsTo(ClubJoinRequest::class, 'request_id');
    }

    /**
     * Câu hỏi
     */
    public function question()
    {
        return $this->belongsTo(ClubJoinFormQuestion::class, 'question_id');
    }

    /**
     * Lấy câu trả lời dạng text (từ answer hoặc answer_json)
     */
    public function getFormattedAnswerAttribute()
    {
        if ($this->answer_json && is_array($this->answer_json)) {
            return implode(', ', $this->answer_json);
        }
        return $this->answer ?? '';
    }
}
