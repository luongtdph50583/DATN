<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClubRecruitmentForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'club_id',
        'name',
        'description',
        'is_active',
        'is_default',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
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
     * Các câu hỏi trong form
     */
    public function questions()
    {
        return $this->hasMany(ClubJoinFormQuestion::class, 'form_id')->orderBy('order');
    }

    /**
     * Các câu hỏi đang hoạt động
     */
    public function activeQuestions()
    {
        return $this->hasMany(ClubJoinFormQuestion::class, 'form_id')
            ->where('is_active', true)
            ->orderBy('order');
    }
}
