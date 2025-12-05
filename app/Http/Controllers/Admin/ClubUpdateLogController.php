<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubUpdateLog;
use Illuminate\Http\Request;

class ClubUpdateLogController extends Controller
{
    public function index()
    {
        $logs = ClubUpdateLog::with(['club', 'admin', 'proposer'])
            ->whereHas('club', function ($q) {
                $q->whereNull('deleted_at'); // chỉ lấy CLB chưa xoá mềm
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.club_update_logs.index', compact('logs'));
    }


    public function show(ClubUpdateLog $log)
    {
        $log->load(['club', 'admin', 'proposer']);

        // Đảm bảo changed_fields được cast đúng
        // Refresh model để đảm bảo cast được áp dụng
        $log->refresh();

        // Debug: Log để kiểm tra
        \Log::info('ClubUpdateLog show', [
            'log_id' => $log->id,
            'changed_fields_type' => gettype($log->changed_fields),
            'changed_fields_is_array' => is_array($log->changed_fields),
            'changed_fields_count' => is_array($log->changed_fields) ? count($log->changed_fields) : 0,
            'changed_fields_raw' => $log->getRawOriginal('changed_fields'),
            'changed_fields_casted' => $log->changed_fields
        ]);

        return view('admin.club_update_logs.show', compact('log'));
    }
}
