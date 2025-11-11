<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'file_name',
        'file_path',
        'file_type',
        'related_id',
        'related_type',
        'uploaded_by'
    ];

    protected $dates = ['deleted_at'];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

      public function related()
    {
        return $this->morphTo(__FUNCTION__, 'related_type', 'related_id');
    }

    // ✅ Scope: chỉ lấy media chưa bị xóa
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    // ✅ Scope: chỉ lấy media đã bị xóa (thùng rác)
    public function scopeTrashed($query)
    {
        return $query->onlyTrashed();
    }
}
