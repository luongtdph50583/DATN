<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClubJoinFormQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'club_id',
        'question',
        'description',
        'type',
        'options',
        'order',
        'is_required',
        'is_active',
        'validation_rules',
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * CLB
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Các câu trả lời cho câu hỏi này
     */
    public function answers()
    {
        return $this->hasMany(ClubJoinFormAnswer::class, 'question_id');
    }
}
