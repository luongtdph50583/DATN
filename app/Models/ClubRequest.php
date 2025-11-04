<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubRequest extends Model
{
    use HasFactory;

    protected $table = 'club_requests';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'field',
        'email',
        'phone',
        'logo',
        'status',
    ];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Người gửi yêu cầu tạo CLB
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by'); // admin xử lý
    }


}
