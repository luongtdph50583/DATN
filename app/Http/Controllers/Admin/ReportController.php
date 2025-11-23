<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EventsBudgetExport;

class ReportController extends Controller
{
    // 1. PDF Báo cáo ngân sách toàn trường
    public function budgetPdf()
    {
        $events = Event::with(['club', 'budgetItems'])
            ->whereHas('budgetItems')
            ->latest()
            ->get();

        $totalEstimated = $events->sum(fn($e) => $e->budgetItems->sum('estimated_cost'));
        $totalSchool    = $events->flatMap->budgetItems->where('type', 'school_fund')->sum('estimated_cost');
        $totalClub      = $events->flatMap->budgetItems->where('type', 'club_fund')->sum('estimated_cost');

        $pdf = Pdf::loadView('admin.reports.budget-pdf', compact('events', 'totalEstimated', 'totalSchool', 'totalClub'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('Bao-cao-ngan-sach-su-kien-' . now()->format('d-m-Y') . '.pdf');
    }

    // 2. Excel chi tiết
    public function budgetExcel()
    {
        return Excel::download(new EventsBudgetExport, 'Ngan-sach-su-kien-chi-tiet-' . now()->format('d-m-Y') . '.xlsx');
    }

    // 3. PDF danh sách điểm danh + QR cho 1 sự kiện
    public function attendancePdf(Event $event)
    {
        $event->load(['registrations.user', 'club']);
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(300)
                    ->generate(route('events.scan', $event->id));

        $pdf = Pdf::loadView('admin.reports.attendance-pdf', compact('event', 'qrCode'));
        return $pdf->stream('Danh-sach-diem-danh-' . $event->id . '.pdf');
    }
}