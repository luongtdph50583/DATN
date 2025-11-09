<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubUpdateLog extends Model
{
    use HasFactory;

    protected $table = 'club_update_logs';

    protected $fillable = [
        'club_id',
        'admin_id',
        'proposer_id',
        'changed_fields', // ✅ trùng với DB, lưu JSON
        'type',           // 'admin' hoặc 'proposer'
        'status',         // 'pending', 'approved', 'rejected'
        'rejected_reason',// lý do từ chối nếu có
    ];

    protected $casts = [
        'changed_fields' => 'array', // tự cast JSON thành array
    ];

    /**
     * Quan hệ đến CLB
     */
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * Quan hệ đến admin (người duyệt)
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Quan hệ đến proposer (người đề xuất)
     */
    public function proposer()
    {
        return $this->belongsTo(User::class, 'proposer_id');
    }
}
