<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubRequestConfirmation extends Model
{
    // Nếu tên bảng không theo chuẩn số nhiều của Laravel
    protected $table = 'club_request_confirmations';

    // Các trường cho phép gán dữ liệu hàng loạt
    protected $fillable = [
        'club_request_id',
        'user_id',
        'status',
        'confirmed_at',
    ];

    // Quan hệ: mỗi confirmation thuộc về một club_request
    public function clubRequest()
    {
        return $this->belongsTo(ClubRequest::class, 'club_request_id');
    }

    // Quan hệ: mỗi confirmation do một user (sinh viên) thực hiện
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
