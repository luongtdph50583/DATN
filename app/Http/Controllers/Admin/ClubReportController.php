<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\Models\Club;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClubReportController extends Controller
{
    public function show($id)
    {
        // Lấy thông tin CLB + quan hệ members, events
        $club = Club::with(['manager','members', 'events.createdBy'])->findOrFail($id);


        // --- THỐNG KÊ THÀNH VIÊN ---
        $totalMembers = $club->members()->count();

          $activeMembers = $club->members->where('status', 'active')->count();
    $inactiveMembers = $club->members->where('status', 'inactive')->count();

        // --- THỐNG KÊ SỰ KIỆN ---
        $totalEvents = $club->events()->count();

        // Nếu bảng events có cột status thì thống kê chi tiết
        $approvedEvents = $club->events()->where('status', 'approved')->count();
        $pendingOrRejected = $club->events()->whereIn('status', ['pending', 'rejected'])->count();

     
        return view('admin.statistics-and-reports.clubsReport', [
    'club' => $club,
    'members' => $club->members, // Quan hệ hasManyThrough hoặc belongsToMany
    'events' => $club->events,   // Quan hệ hasMany
    'totalMembers' => $club->members->count(),
   'activeMembers' => $activeMembers,
        'inactiveMembers' => $inactiveMembers,
    'totalEvents' => $club->events->count(),
    'approvedEvents' => $club->events->where('status', 'approved')->count(),
    'pendingOrRejected' => $club->events->whereIn('status', ['pending','rejected'])->count(),
]);

    }

    // --- (TÙY CHỌN) XUẤT PDF ---
   public function exportPdf($id)
{
    $club = Club::with(['members', 'events'])->findOrFail($id);

    // --- THỐNG KÊ THÀNH VIÊN ---
    $totalMembers = $club->members()->count();
     $activeMembers = $club->members->where('status', 'active')->count();
    $inactiveMembers = $club->members->where('status', 'inactive')->count();

    // --- THỐNG KÊ SỰ KIỆN ---
    $totalEvents = $club->events()->count();
    $approvedEvents = $club->events()->where('status', 'approved')->count();
    $pendingOrRejected = $club->events()->whereIn('status', ['pending', 'rejected'])->count();

    // --- XUẤT PDF ---
    $pdf = PDF::loadView('admin.statistics-and-reports.report_pdf', [
        'club' => $club,
        'members' => $club->members,
        'events' => $club->events,
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
