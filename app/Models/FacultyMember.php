<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyMember extends Model
{
    // Tên bảng nếu khác tên mặc định 'faculty_members'
    protected $table = 'faculty_members';

    // Các trường có thể mass assign
    protected $fillable = [
        'user_id',
        'employee_code',
        'department',
        'title',
        'position',
        'office_phone',
        'phone_personal',
        'email_official',
        'office_location',
        'verified',
        'status',
        'start_date',
    ];

    /**
     * Quan hệ với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
