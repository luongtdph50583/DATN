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
        'batch_id', // ✅ phải có đây

    ];

    /**
     * Quan hệ: email này thuộc về người dùng nào
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: rút gọn nội dung khi hiển thị trong danh sách
     */
    public function getShortContentAttribute()
    {
        return str($this->content)->limit(80);
    }
}
