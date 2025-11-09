<?php

namespace App\Services;

use App\Models\Club;
use App\Models\ClubUpdateLog;

class ClubUpdateLogService
{
    /**
     * Lưu log khi admin cập nhật CLB
     *
     * @param int $clubId
     * @param int $adminId
     * @param array $changes
     * @param int|null $proposerId
     * @return ClubUpdateLog
     */
    public function logUpdate(int $clubId, int $adminId, array $changes, ?int $proposerId = null, string $type = 'admin')
    {
        return ClubUpdateLog::create([
            'club_id' => $clubId,
            'admin_id' => $adminId,
            'proposer_id' => $proposerId,
            'changes' => $changes,
        ]);
    }
    public function logAdminUpdate(Club $club, array $changes, int $adminId)
    {
        ClubUpdateLog::create([
            'club_id' => $club->id,
            'admin_id' => $adminId, // người sửa
            'proposer_id' => null,  // admin tự sửa
            'changed_fields' => json_encode($changes, JSON_UNESCAPED_UNICODE),
        ]);

    }





}
