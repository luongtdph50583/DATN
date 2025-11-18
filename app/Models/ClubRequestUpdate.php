<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubRequestUpdate extends Model
{
    protected $fillable = [
        'club_id',
        'name',
        'slogan',
        'description',
        'field',
        'member_limit',
        'manager_id',
        'advisor_id',
        'email',
        'phone',
        'logo',
        'rules',
        'location',
        'advisor_status',
        'reason',
        'user_id',
    ];

    // 🔹 CLB gốc (để lấy thông tin hiện tại)
    public function club()
    {
        return $this->belongsTo(Club::class, 'club_id')
            ->with(['clubMembers.user', 'clubMembers.memberInfo', 'advisorFaculty']);
    }

    // 🔹 Các thành viên được đề xuất cập nhật
    public function memberUpdates()
    {
        return $this->hasMany(ClubRequestMemberUpdate::class, 'club_request_update_id')
            ->with(['user', 'memberInfo']);
    }

    // 🔹 Người đề xuất cập nhật CLB
    public function proposer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔹 Giảng viên đề xuất mới (user)
    public function advisorProposedUser()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    // 🔹 Giảng viên đề xuất mới (faculty_members)
    public function advisorFacultyProposed()
    {
        return $this->belongsTo(FacultyMember::class, 'advisor_id', 'id');
    }

    // 🔹 Lấy ban quản lý hiện tại của CLB (shortcut)
    public function currentManagement()
    {
        return $this->club ? $this->club->clubMembers : collect();
    }

    // 🔹 Giảng viên hiện tại của CLB
    public function currentAdvisor()
    {
        return $this->club && $this->club->advisorFaculty
            ? $this->club->advisorFaculty
            : null;
    }
    public function advisorProposed()
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }
    // Chủ nhiệm đề xuất (manager_id trong club_request_updates)
    public function managerProposed()
    {
        return $this->belongsTo(User::class, 'manager_id', 'id');
    }


}
