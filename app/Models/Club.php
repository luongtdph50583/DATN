<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Club extends Model
{
    use SoftDeletes;

protected $fillable = [
        'name',
        'description',
        'logo',
        'field',
        'status',
        'manager_id',
        'advisor_id', // thêm nếu muốn cho phép mass assign
        'email',
        'phone',
        'member_limit',
        'founded_at',
        'location',
        'rules',
        'deleted_reason',
        'slogan'
    ];

    protected $casts = [
        'founded_at' => 'date',
        'description' => 'string',
    ];

    /** Người quản lý / chủ nhiệm CLB */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }


public function members()
{
    return $this->belongsToMany(
        User::class,
        'club_members',
        'club_id',
        'member_id'   
    )->withPivot('role', 'status', 'joined_at');
}

public function activeMembers()
{
    return $this->belongsToMany(User::class, 'club_members', 'club_id', 'member_id')
        ->wherePivot('status', 'active')           // ← đúng cách
        ->withPivot('role', 'status', 'joined_at');
}
    /** Bài viết CLB */
    public function posts()
    {
        return $this->hasMany(Post::class, 'club_id');
    }

    /** Sự kiện CLB */
    public function events()
    {
        return $this->hasMany(Event::class, 'club_id');
    }

    /** Giao dịch quỹ */
    public function fundTransactions()
    {
        return $this->hasMany(FundTransaction::class, 'club_id');
    }

    /** Quỹ CLB */
    public function fund()
    {
        return $this->hasOne(Fund::class);
    }

    /** Tự động tạo quỹ khi tạo CLB mới */

    public function clubMembers()
    {
        return $this->hasMany(ClubMember::class, 'club_id');
    }


    /** Yêu cầu tham gia CLB */
    public function joinRequests()
    {
        return $this->hasMany(ClubJoinRequest::class, 'club_id');
    }
    public function documents()
    {
        return $this->hasMany(Document::class, 'clb_id');
    }
    // app/Models/Club.php

    // app/Models/Club.php
    public function advisor()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }
    public function advisorFaculty()
    {
        // advisor_id = faculty_members.id
        return $this->belongsTo(FacultyMember::class, 'advisor_id')->with('user');
    }

    /**
     * Câu hỏi form tuyển thành viên
     */
    public function joinFormQuestions()
    {
        return $this->hasMany(ClubJoinFormQuestion::class, 'club_id');
    }

    /**
     * Lịch phỏng vấn
     */
    public function interviewSchedules()
    {
        return $this->hasMany(ClubInterviewSchedule::class, 'club_id');
    }
}
