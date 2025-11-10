<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\EventFundRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventFundRequestController extends Controller
{
    // 1️⃣ Hiển thị danh sách yêu cầu
public function index(Request $request)
{
    $query = EventFundRequest::with(['event.club', 'requestedBy', 'approvedBy', 'disbursedBy'])
        ->orderBy('created_at', 'desc');
 // Tổng chi: tổng approved_amount của các giao dịch đã duyệt
    $totalExpense = EventFundRequest::whereIn('status', ['disbursing','disbursed'])
        ->sum('approved_amount');

    // Số lượng đang chờ giải ngân
    $pendingCount = EventFundRequest::where('status', 'pending_disbursement')->count();

    // Lọc theo CLB
    if ($request->filled('club_id')) {
        $query->whereHas('event', function($q) use ($request) {
            $q->where('club_id', $request->club_id);
        });
    }

    // Lọc theo trạng thái
    if ($request->filled('status')) {
        // Chỉ nhận 2 trạng thái: pending_disbursement (Chờ giải ngân) và disbursed (Đã giải ngân)
        if (in_array($request->status, ['pending_disbursement','disbursed'])) {
            $query->where('status', $request->status);
        }
    }

    // Lọc theo ngày tạo
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $transactions = $query->paginate(20);

    $clubs = Club::all(); // để hiển thị select filter

    return view('admin.funds.index', [
        'transactions' => $transactions,
        'clubs' => $clubs,
         'totalExpense' => $totalExpense,
        'pendingCount' => $pendingCount,
    ]);
}



    // 2️⃣ Form tạo mới
    public function create()
    {
        $events = Event::all();
        $clubs = Club::all();
        $sourceTypes = ['school' => 'Quỹ nhà trường', 'sponsor' => 'Nhà tài trợ', 'club' => 'Quỹ CLB'];
return view('admin.event_funds.requests.create', compact('events', 'clubs', 'sourceTypes'));
    }

    // 3️⃣ Lưu yêu cầu mới
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'source_type' => 'required|in:school,sponsor,club',
            'amount_requested' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        EventFundRequest::create([
            'event_id' => $request->event_id,
            'source_type' => $request->source_type,
            'amount_requested' => $request->amount_requested,
            'note' => $request->note,
            'requested_by' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('admin.event_fund_requests.index')
                         ->with('success', 'Đã thêm yêu cầu cấp kinh phí.');
    }

    // 4️⃣ Form chỉnh sửa
    public function edit($id)
    {
        $request = EventFundRequest::findOrFail($id);
        $events = Event::all();
        return view('admin.event_funds.requests.edit', compact('request', 'events'));
    }

    // 5️⃣ Cập nhật yêu cầu (chỉ update thông tin, không thay đổi status đã duyệt)
    public function update(Request $request, $id)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'amount_requested' => 'required|numeric|min:0',
        ]);

        $fundRequest = EventFundRequest::findOrFail($id);

        // Không thay đổi status nếu đã approved
        $fundRequest->event_id = $request->event_id;
        $fundRequest->amount_requested = $request->amount_requested;
        $fundRequest->note = $request->note ?? $fundRequest->note;
        $fundRequest->save();

        return redirect()->route('admin.event_fund_requests.index')
                         ->with('success', 'Cập nhật yêu cầu cấp kinh phí thành công!');
    }

    // 6️⃣ Xóa yêu cầu
    public function destroy($id)
    {
        $fundRequest = EventFundRequest::findOrFail($id);
        $fundRequest->delete();

        return redirect()->route('admin.event_fund_requests.index')
                         ->with('success', 'Đã xóa yêu cầu cấp kinh phí.');
    }

    // 7️⃣ Form duyệt yêu cầu
    public function approveForm($id)
    {
        $request = EventFundRequest::with('event')->findOrFail($id);
        return view('admin.event_funds.requests.approve', compact('request'));
    }

    // 8️⃣ Duyệt yêu cầu và cộng quỹ
public function approve(Request $request, $id)
{
    $fundRequest = EventFundRequest::findOrFail($id);

    if ($fundRequest->status === 'disbursed' || $fundRequest->status === 'approved') {
        return redirect()->back()->with('info', 'Yêu cầu này đã được duyệt.');
    }

    // Validate
    $request->validate([
        'approved_amount' => 'required|numeric|min:0|max:' . $fundRequest->amount_requested,
        'disbursed_by' => 'required|exists:users,id',
        'disbursement_date' => 'required|date',
        'disbursement_proof' => 'nullable|array',
        'disbursement_proof.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120',
    ]);

    $fundRequest->approved_amount = $request->approved_amount;
    $fundRequest->status = 'disbursed';
    $fundRequest->approved_by = auth()->id();
    $fundRequest->disbursed_by = $request->disbursed_by;
    $fundRequest->disbursement_date = $request->disbursement_date;

    if($request->hasFile('disbursement_proof')) {
        $files = [];
        foreach($request->file('disbursement_proof') as $file) {
            $files[] = $file->store('disbursement_proofs', 'public');
        }
        $fundRequest->disbursement_proof = json_encode($files);
    }

    $fundRequest->save();

    return redirect()->route('admin.event_fund_requests.index')
                     ->with('success', 'Yêu cầu đã được duyệt và lưu thông tin giải ngân thành công.');
}



    // 9️⃣ Từ chối / hủy yêu cầu
public function showRejectForm($id)
{
    $request = EventFundRequest::findOrFail($id);
    return view('admin.event_funds.requests.reject', compact('request'));
}

// Xử lý POST từ chối
public function reject(Request $request, $id)
{
    $requestFund = EventFundRequest::findOrFail($id);

    $request->validate([
        'rejection_reason' => 'required|string|max:500',
    ]);

    $requestFund->update([
        'status' => 'rejected',
        'rejection_reason' => $request->rejection_reason,
        'approved_by' => auth()->id(),
    ]);

    return redirect()->route('admin.funds.index')->with('success', 'Yêu cầu đã bị từ chối thành công.');
}


    // 10️⃣ Xem chi tiết yêu cầu
    public function show($id)
    {
        $request = EventFundRequest::with(['event', 'user'])->findOrFail($id);
        return view('admin.event_funds.requests.show', compact('request'));
    }
}
