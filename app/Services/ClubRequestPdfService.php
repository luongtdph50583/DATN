<?php

namespace App\Services;

use App\Models\ClubRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ClubRequestPdfService
{
    /**
     * Sinh PDF cho đơn thành lập CLB
     */
    public function generatePdf(ClubRequest $clubRequest)
    {
        // Load tất cả relationships cần thiết
        $clubRequest->load([
            'creator.student',
            'members.student',
            'confirmations.user.student',
            'approvals.admin',
            'clubManager.student',
            'deputyManager.student',
            'secretary.student',
            'treasurer.student',
            'eventManager.student',
            'communication.student'
        ]);

        // Render PDF từ view
        $pdf = Pdf::loadView('pdfs.club_request', [
            'request' => $clubRequest
        ]);

        // Set paper size
        $pdf->setPaper('a4', 'portrait');

        // Đặt tên file
        $filename = 'club_request_' . $clubRequest->id . '_' . time() . '.pdf';
        $path = 'club_requests/pdfs/' . $filename;

        // Lưu file vào storage/app/public
        Storage::disk('public')->put($path, $pdf->output());

        // Cập nhật vào database
        $clubRequest->update([
            'pdf_file' => $path,
            'pdf_generated_at' => now(),
        ]);

        return $path;
    }

    /**
     * Download PDF
     */
    public function downloadPdf(ClubRequest $clubRequest)
    {
        // Nếu chưa có PDF, sinh mới
        if (!$clubRequest->pdf_file || !Storage::disk('public')->exists($clubRequest->pdf_file)) {
            $this->generatePdf($clubRequest);
        }

        $downloadName = 'don_thanh_lap_clb_' . $clubRequest->id . '.pdf';

        return Storage::disk('public')->download($clubRequest->pdf_file, $downloadName);
    }

    /**
     * Xem PDF trực tiếp trên browser
     */
    public function viewPdf(ClubRequest $clubRequest)
    {
        $clubRequest->load([
            'creator.student',
            'members.student',
            'confirmations.user.student',
            'approvals.admin',
            'clubManager.student',
            'deputyManager.student',
            'secretary.student',
            'treasurer.student',
            'eventManager.student',
            'communication.student'
        ]);

        $pdf = Pdf::loadView('pdfs.club_request', ['request' => $clubRequest]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('club_request_' . $clubRequest->id . '.pdf');
    }
}
