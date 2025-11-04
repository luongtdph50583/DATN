<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    protected $fillable = [
        'club_id',
        'user_id',
        'title',
        'content',
        'type',
        'status',
        'visibility',
        'thumbnail'
    ];

    protected $morphClass = 'post'; // nếu bạn dùng morphMap, giữ nguyên

    // ✅ Quan hệ với CLB
    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    // ✅ Quan hệ với người đăng
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
