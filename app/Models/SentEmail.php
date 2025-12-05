<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'status',
        'batch_id',
        'sender_id', // ✅ Đã có rồi
    ];

    /**
     * Quan hệ: email này thuộc về người dùng nào (người nhận)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Quan hệ: email này được gửi bởi ai (người gửi)
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Accessor: rút gọn nội dung khi hiển thị trong danh sách
     */
    public function getShortContentAttribute()
    {
        return str($this->content)->limit(80);
    }
}