<?php

namespace App\Services;

use App\Models\Club;
use App\Models\ClubUpdateLog;

class ClubUpdateLogService
{
    /**
     * Người dùng gửi đề xuất thay đổi CLB
     */
   
    public function proposeUpdate(int $clubId, int $userId, array $newData): ClubUpdateLog
    {
        // Lấy dữ liệu CLB hiện tại
        $club = Club::with('clubMembers.member')->findOrFail($clubId);

        // Danh sách các role
        $roles = ['club_manager', 'deputy_manager', 'secretary', 'treasurer', 'event_manager', 'communication', 'member'];

        // Chuẩn bị JSON changed_fields
        $changes = [];

        // Các trường cơ bản
        $fields = ['name', 'slogan', 'description', 'field', 'purpose', 'rule', 'member_limit'];
        foreach ($fields as $field) {
            $changes[$field] = [
                'old' => $club->$field,
                'new' => $newData[$field] ?? $club->$field,
            ];
        }

        // Giảng viên đỡ đầu
        $changes['advisorFaculty'] = [
            'old' => $club->advisor?->id,
            'new' => $newData['advisor_id'] ?? $club->advisor?->id,
        ];

        // Ban quản lý
        foreach ($roles as $role) {
            $member = $club->clubMembers->firstWhere('role', $role);
            $changes[$role] = [
                'old' => $member?->member_id,
                'new' => $newData[$role] ?? $member?->member_id,
            ];
        }

        // Tạo log đề xuất
        return ClubUpdateLog::create([
            'club_id' => $club->id,
            'proposer_id' => $userId,
            'changed_fields' => $changes, // nhờ $casts = ['changed_fields'=>'array'] tự json_encode
            'type' => 'proposer',
            'status' => 'pending',
        ]);
    }


    /**
     * Admin duyệt đề xuất
     */
    public function approveProposal(ClubUpdateLog $proposal, int $adminId): Club
    {
        $club = Club::findOrFail($proposal->club_id);

        // Cập nhật thông tin CLB
        foreach ($proposal->changed_fields as $field => $values) {
            $club->$field = $values['new'] ?? null;
        }
        $club->save();

        // Cập nhật log
        $proposal->admin_id = $adminId;
        $proposal->type = 'admin';
        $proposal->status = 'approved';
        $proposal->save();

        return $club;
    }

    /**
     * Admin từ chối đề xuất
     */
    public function rejectProposal(ClubUpdateLog $proposal, int $adminId, string $reason): ClubUpdateLog
    {
        $proposal->admin_id = $adminId;
        $proposal->status = 'rejected';
        $proposal->rejected_reason = $reason;
        $proposal->save();

        return $proposal;
    }

    /**
     * Admin tự chỉnh sửa CLB trực tiếp
     */
    public function logAdminUpdate(Club $club, array $changes, int $adminId): ClubUpdateLog
    {
        return ClubUpdateLog::create([
            'club_id' => $club->id,
            'admin_id' => $adminId,
            'proposer_id' => null,
            'changed_fields' => json_encode($changes, JSON_UNESCAPED_UNICODE),
            'type' => 'admin',
            'status' => 'approved',
        ]);
    }
}
