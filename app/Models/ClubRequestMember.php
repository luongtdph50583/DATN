<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubRequestMember extends Model
{
    use HasFactory;

    protected $table = 'club_request_members';

    protected $fillable = [
        'club_request_id',
        'user_id',
        'role',
    ];

    /**
     * Yêu cầu CLB mà thành viên này thuộc về
     */
    public function clubRequest()
    {
        return $this->belongsTo(ClubRequest::class, 'club_request_id');
    }

    /**
     * User nếu có
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Lấy thông tin chi tiết member từ user
     */
   
}
