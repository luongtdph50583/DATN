<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventFundRequest;
use App\Models\EventFundSettlement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventFundSettlementController extends Controller
{
    // 1️⃣ Danh sách settlement
    public function index()
    {
        $settlements = EventFundSettlement::with('fundRequest.event', 'reviewer')
                        ->orderByDesc('created_at')
                        ->get();
        return view('admin.event_funds.settlements.index', compact('settlements'));
    }

    // 2️⃣ Form tạo mới
    public function create()
    {
        // Lấy các request đã được duyệt
        $approvedRequests = EventFundRequest::where('status', 'approved')
                            ->with('event')
                            ->get();

        return view('admin.event_funds.settlements.create', compact('approvedRequests'));
    }

    // 3️⃣ Lưu settlement mới
    public function store(Request $request)
    {
        $request->validate([
            'fund_request_id' => 'required|exists:event_fund_requests,id',
            'total_spent' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'receipts.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'status' => 'nullable|in:pending_review,approved,needs_revision',
        ]);

        $fundRequest = EventFundRequest::findOrFail($request->fund_request_id);

        $data = [
            'fund_request_id' => $fundRequest->id,
            'total_spent' => $request->total_spent,
            'difference' => $request->total_spent - $fundRequest->amount_requested,
            'details' => $request->details ? json_decode($request->details, true) : null,
            'status' => $request->status ?? 'pending_review',
        ];

        // Upload receipts
        if ($request->hasFile('receipts')) {
            $files = [];
            foreach ($request->file('receipts') as $file) {
                $files[] = $file->store('receipts', 'public');
            }
            $data['receipts'] = $files; // Laravel sẽ cast array -> JSON
        }

        EventFundSettlement::create($data);

        return redirect()->route('admin.event_fund_settlements.index')
                         ->with('success', 'Tạo quyết toán thành công!');
    }

    // 4️⃣ Xem chi tiết
    public function show($id)
    {
        $settlement = EventFundSettlement::with('fundRequest.event', 'reviewer')->findOrFail($id);
        return view('admin.event_funds.settlements.show', compact('settlement'));
    }

    // 5️⃣ Form edit
    public function edit($id)
    {
        $settlement = EventFundSettlement::findOrFail($id);

        $approvedRequests = EventFundRequest::where('status', 'approved')
                            ->with('event')
                            ->get();

        return view('admin.event_funds.settlements.edit', compact('settlement', 'approvedRequests'));
    }

    // 6️⃣ Update settlement
    public function update(Request $request, $id)
    {
        $request->validate([
            'fund_request_id' => 'required|exists:event_fund_requests,id',
            'total_spent' => 'required|numeric|min:0',
            'details' => 'nullable|string',
            'receipts.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'status' => 'required|in:pending_review,approved,needs_revision',
        ]);

        $settlement = EventFundSettlement::findOrFail($id);
        $fundRequest = EventFundRequest::findOrFail($request->fund_request_id);

        $data = [
            'fund_request_id' => $fundRequest->id,
            'total_spent' => $request->total_spent,
            'difference' => $request->total_spent - $fundRequest->amount_requested,
            'details' => $request->details ? json_decode($request->details, true) : null,
            'status' => $request->status,
        ];

        // Merge receipts mới với cũ
        $existingFiles = $settlement->receipts ?? [];
        if ($request->hasFile('receipts')) {
            foreach ($request->file('receipts') as $file) {
                $existingFiles[] = $file->store('receipts', 'public');
            }
        }
        $data['receipts'] = $existingFiles;

        // Lưu reviewer nếu status được duyệt hoặc cần chỉnh sửa
        if (in_array($data['status'], ['approved', 'needs_revision'])) {
            $data['reviewed_by'] = Auth::id();
            $data['reviewed_at'] = now();
        }

        $settlement->update($data);

        return redirect()->route('admin.event_fund_settlements.index')
                         ->with('success', 'Cập nhật quyết toán thành công!');
    }

    // 7️⃣ Xóa settlement
    public function destroy($id)
    {
        $settlement = EventFundSettlement::findOrFail($id);

        // Xóa file cũ
        if ($settlement->receipts) {
            foreach ($settlement->receipts as $file) {
                if (Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $settlement->delete();

        return redirect()->route('admin.event_fund_settlements.index')
                         ->with('success', 'Xóa quyết toán thành công!');
    }
}
