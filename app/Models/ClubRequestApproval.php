<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubRequestApproval extends Model
{
    // Nếu tên bảng không theo chuẩn số nhiều của Laravel
    protected $table = 'club_request_approvals';

    // Các trường cho phép gán dữ liệu hàng loạt
    protected $fillable = [
        'club_request_id',
        'admin_id',
        'status',
        'approved_at',
    ];

    // Quan hệ: mỗi approval thuộc về một club_request
    public function clubRequest()
    {
        return $this->belongsTo(ClubRequest::class, 'club_request_id');
    }

    // Quan hệ: mỗi approval do một admin thực hiện
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
