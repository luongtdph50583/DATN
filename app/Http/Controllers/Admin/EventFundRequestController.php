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
    public function index()
    {
        $requests = EventFundRequest::with(['event', 'user'])->latest()->get();
        return view('admin.event_funds.requests.index', compact('requests'));
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

        // Chỉ duyệt nếu chưa được duyệt
        if ($fundRequest->status === 'approved') {
            return redirect()->back()->with('info', 'Yêu cầu này đã được duyệt.');
        }

        $request->validate([
            'approved_amount' => 'required|numeric|min:0|max:' . $fundRequest->amount_requested,
        ]);

        $approvedAmount = $request->approved_amount;

        // Cập nhật approved_amount và status
        $fundRequest->approved_amount = $approvedAmount;
        $fundRequest->status = 'approved';
        $fundRequest->approved_by = auth()->id();
        $fundRequest->save();

        // Cập nhật quỹ event
        $event = $fundRequest->event;
        $event->budget_estimated += $approvedAmount;
        $event->budget_current   += $approvedAmount;
        $event->save();

        return redirect()->route('admin.event_fund_requests.index')
                         ->with('success', 'Đã duyệt yêu cầu và cập nhật quỹ event thành công.');
    }

    // 9️⃣ Từ chối / hủy yêu cầu
    public function reject($id)
    {
        $fundRequest = EventFundRequest::findOrFail($id);

        // Chỉ từ chối nếu chưa duyệt
        if ($fundRequest->status === 'approved') {
            return redirect()->back()->with('error', 'Yêu cầu đã được duyệt, không thể từ chối.');
        }

        $fundRequest->status = 'rejected';
        $fundRequest->save();

        return redirect()->route('admin.event_fund_requests.index')
                         ->with('success', 'Yêu cầu đã được từ chối.');
    }

    // 10️⃣ Xem chi tiết yêu cầu
    public function show($id)
    {
        $request = EventFundRequest::with(['event', 'user'])->findOrFail($id);
        return view('admin.event_funds.requests.show', compact('request'));
    }
}
