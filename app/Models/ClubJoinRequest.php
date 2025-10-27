<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubJoinRequest extends Model
{
    use HasFactory;

    // ⚠️ Thêm dòng này để chỉ định tên bảng đúng
    protected $table = 'club_join_requests';

    // Nếu bạn có fillable fields:
    protected $fillable = [
        'club_id',
        'user_id',
        'status',
        'requested_at',
    ];
    protected $casts = [
        'requested_at' => 'datetime',
    ];
    // Quan hệ (tuỳ bạn có muốn không)
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
