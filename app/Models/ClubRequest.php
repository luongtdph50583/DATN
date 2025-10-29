<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',        // tên CLB trong DB
        'description',
        'field',
        'status',
        'logo'         // nếu bạn lưu đường dẫn logo ở đây
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
