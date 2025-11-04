<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'clb_id',
        'uploaded_by',
        'access_level',      // JSON: nhiều cấp truy cập
        'tags',
        'status',            // 'pending', 'approved', 'rejected'
        'approved_by',       // ID người duyệt
        'approved_at',       // thời điểm duyệt
        'rejected_reason',   // lý do từ chối
    ];

    protected $casts = [
        'access_level' => 'array',       // Tự động decode JSON thành array
        'approved_at' => 'datetime',     // Tự động thành Carbon
    ];

    // Quan hệ với CLB
    public function club()
    {
        return $this->belongsTo(Club::class, 'clb_id');
    }

    // Quan hệ với người tải lên
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Quan hệ với người duyệt (nếu có)
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope lọc theo trạng thái
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Trả về danh sách quyền truy cập dạng chuỗi
    public function getAccessLevelTextAttribute()
    {
        return is_array($this->access_level)
            ? implode(', ', $this->access_level)
            : $this->access_level;
    }
}
