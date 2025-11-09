<?php

namespace  App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubUpdateLog;
use Illuminate\Http\Request;

class ClubUpdateLogController extends Controller
{
    public function index()
    {
        // Lấy danh sách log, sắp xếp mới nhất lên đầu, kèm quan hệ admin, proposer, club
        $logs = ClubUpdateLog::with(['club', 'admin', 'proposer'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.club_update_logs.index', compact('logs'));
    }
}
