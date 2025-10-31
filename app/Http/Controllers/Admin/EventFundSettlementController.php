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
        $event = $fundRequest->event;

        // Xử lý details JSON
        $details = null;
        if ($request->filled('details')) {
            $json = json_decode($request->details, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $details = $json;
            } else {
                return back()->withErrors(['details' => 'JSON không hợp lệ']);
            }
        }

        // Upload receipts
        $files = [];
        if ($request->hasFile('receipts')) {
            foreach ($request->file('receipts') as $file) {
                $files[] = $file->store('settlements/receipts', 'public');
            }
        }

        // Tạo settlement
        $settlement = EventFundSettlement::create([
            'fund_request_id' => $fundRequest->id,
            'total_spent' => $request->total_spent,
            'difference' => $request->total_spent - $fundRequest->amount_requested,
            'details' => $details,
            'receipts' => !empty($files) ? json_encode($files) : null,
            'status' => $request->status ?? 'pending_review',
        ]);

        // Nếu admin duyệt ngay khi tạo
        if ($settlement->status === 'approved') {
            $event->budget_current -= $settlement->total_spent;
            $event->budget_used += $settlement->total_spent;
            $event->save();

            $settlement->reviewed_by = Auth::id();
            $settlement->reviewed_at = now();
            $settlement->save();
        }

        return redirect()->route('admin.event_fund_settlements.index')
            ->with('success', 'Tạo quyết toán thành công!');
    }

    // 4️⃣ Xem chi tiết settlement
    public function show($id)
    {
        $settlement = EventFundSettlement::with('fundRequest.event', 'reviewer')->findOrFail($id);
        return view('admin.event_funds.settlements.show', compact('settlement'));
    }

    // 5️⃣ Form edit settlement
    public function edit($id)
    {
        $settlement = EventFundSettlement::findOrFail($id);
        $approvedRequests = EventFundRequest::where('status', 'approved')->with('event')->get();

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
        'keep_receipts.*' => 'sometimes|string',
    ]);

    $settlement = EventFundSettlement::findOrFail($id);
    $fundRequest = EventFundRequest::findOrFail($request->fund_request_id);
    $event = $fundRequest->event;

    // Chi tiết JSON
    $details = null;
    if ($request->filled('details')) {
        $json = json_decode($request->details, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $details = $json;
        } else return back()->withErrors(['details' => 'JSON không hợp lệ']);
    }

    // Xử lý receipts
$oldReceipts = is_array($settlement->receipts) 
    ? $settlement->receipts 
    : (is_string($settlement->receipts) ? json_decode($settlement->receipts, true) : []);

    $keepReceipts = $request->input('keep_receipts', []);

    // Xóa file đã bị remove
    foreach (array_diff($oldReceipts, $keepReceipts) as $file) {
        Storage::disk('public')->delete($file);
    }

    // Thêm file mới
    if ($request->hasFile('receipts')) {
        foreach ($request->file('receipts') as $file) {
            $keepReceipts[] = $file->store('settlements/receipts', 'public');
        }
    }

    // Cập nhật settlement
    $settlement->update([
        'fund_request_id' => $fundRequest->id,
        'total_spent' => $request->total_spent,
        'difference' => $request->total_spent - $fundRequest->amount_requested,
        'details' => $details,
        'receipts' => !empty($keepReceipts) ? json_encode($keepReceipts) : null,
        'status' => $request->status,
        'reviewed_by' => $request->status !== 'pending_review' ? Auth::id() : null,
        'reviewed_at' => $request->status !== 'pending_review' ? now() : null,
    ]);

    return redirect()->route('admin.event_fund_settlements.index')->with('success', 'Cập nhật quyết toán thành công!');
}

    // 7️⃣ Xóa settlement
    public function destroy($id)
    {
        $settlement = EventFundSettlement::findOrFail($id);

        if ($settlement->receipts) {
            $receipts = is_string($settlement->receipts) ? json_decode($settlement->receipts, true) : $settlement->receipts;
            if (is_array($receipts)) {
                foreach ($receipts as $file) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $settlement->delete();

        return redirect()->route('admin.event_fund_settlements.index')
            ->with('success', 'Xóa quyết toán thành công!');
    }
}
