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
        'slogan',
        'description',
        'field',
        'plan',
        'purpose',
        'email',
        'phone',
        'logo',
        'advisor_id',
        'advisor_status',
        'status',
        'handled_by',
        'note',
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

    /**
     * Admin xử lý yêu cầu
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Thành viên/ban quản lý đề xuất trong yêu cầu CLB
     */
    public function clubRequestMembers()
    {
        return $this->hasMany(ClubRequestMember::class, 'club_request_id');
    }

    /**
     * Giảng viên đỡ đầu (liên kết với user)
     */
    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    /**
     * Giảng viên đỡ đầu chi tiết (faculty_member)
     */
    public function advisorFaculty()
    {
        // advisor_id = faculty_members.id
        return $this->belongsTo(FacultyMember::class, 'advisor_id')->with('user');
    }
    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id');
    }


}
