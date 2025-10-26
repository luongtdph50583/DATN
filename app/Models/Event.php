<?php

     namespace App\Models;

     use Illuminate\Database\Eloquent\Model;


class Event extends Model
{
protected $fillable = [
        'club_id',
        'name',
        'description',
        'start_time', // SỬA: Thay thế event_date
        'end_time',   // THÊM: Cột thời gian kết thúc
        'location',
        'max_participants', // THÊM: Giới hạn người tham gia
        'is_public',      // THÊM: Công khai/Nội bộ
        'status',
        'budget',       // THÊM: Ngân sách sự kiện
        'created_by',
        'approval_by',  // THÊM: Người duyệt
        'media_id',     // THÊM: Liên kết Media
    ];
   protected $casts = [
        'start_time' => 'datetime', // SỬA: Ép kiểu cho start_time
        'end_time' => 'datetime',   // THÊM: Ép kiểu cho end_time
        'is_public' => 'boolean',   // THÊM: Ép kiểu cho is_public
        'budget' => 'decimal:2',    // THÊM: Ép kiểu decimal (10, 2)
        'status' => 'string',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }
}



