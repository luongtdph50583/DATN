<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'club_id',
        'user_id',
        'title',
        'content',
        'type',
        'status',
        'visibility',
        'thumbnail',
        'is_visible',
        'is_featured',
        'approved_by',
        'approved_at',
        'rejection_reason'
        
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_featured' => 'boolean',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    protected $dates = [
        'approved_at',
        'published_at',
        'deleted_at',
    ];

    protected $morphClass = 'post'; // nếu bạn dùng morphMap, giữ nguyên

    // ✅ Quan hệ với CLB
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
    // Trong Post.php
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ✅ Quan hệ với người đăng
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ Quan hệ với admin duyệt bài
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ✅ Media đang hoạt động (chưa bị xóa)
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'related')->whereNull('deleted_at');
    }

    // ✅ Media đã bị xóa mềm (thùng rác)
    public function trashedMedia(): MorphMany
    {
        return $this->morphMany(Media::class, 'related')->onlyTrashed();
    }

    // ✅ Media đầy đủ (cả đang hoạt động và đã xóa)
    public function allMedia(): MorphMany
    {
        return $this->morphMany(Media::class, 'related')->withTrashed();
    }
}
