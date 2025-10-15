<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;

class ClubReportController extends Controller
{
    public function show($id)
    {
        // Lấy thông tin CLB + quan hệ members, events
        $club = Club::with(['members', 'events'])->findOrFail($id);

        // --- THỐNG KÊ THÀNH VIÊN ---
        $totalMembers = $club->members()->count();

        // Nếu bảng club_members chưa có cột status -> tạm đặt mặc định
        $activeMembers = 0;   
        $inactiveMembers = 0;

        // --- THỐNG KÊ SỰ KIỆN ---
        $totalEvents = $club->events()->count();

        // Nếu bảng events có cột status thì thống kê chi tiết
        $approvedEvents = $club->events()->where('status', 'approved')->count();
        $pendingOrRejected = $club->events()->whereIn('status', ['pending', 'rejected'])->count();

        return view('admin.statistics-and-reports.clubsReport', compact(
            'club',
            'totalMembers',
            'activeMembers',
            'inactiveMembers',
            'totalEvents',
            'approvedEvents',
            'pendingOrRejected'
        ));
    }

    // --- (TÙY CHỌN) XUẤT PDF ---
   public function exportPdf($id)
{
    $club = Club::with(['members', 'events'])->findOrFail($id);

    // --- THỐNG KÊ THÀNH VIÊN ---
    $totalMembers = $club->members()->count();
    $activeMembers = 0;
    $inactiveMembers = 0;

    // --- THỐNG KÊ SỰ KIỆN ---
    $totalEvents = $club->events()->count();
    $approvedEvents = $club->events()->where('status', 'approved')->count();
    $pendingOrRejected = $club->events()->whereIn('status', ['pending', 'rejected'])->count();

    // --- XUẤT PDF ---
    $pdf = \PDF::loadView('admin.statistics-and-reports.report_pdf', [
        'club' => $club,
        'totalMembers' => $totalMembers,
        'activeMembers' => $activeMembers,
        'inactiveMembers' => $inactiveMembers,
        'totalEvents' => $totalEvents,
        'approvedEvents' => $approvedEvents,
        'pendingOrRejected' => $pendingOrRejected
    ]);

    return $pdf->download('bao_cao_clb_' . $club->name . '.pdf');
}

}
