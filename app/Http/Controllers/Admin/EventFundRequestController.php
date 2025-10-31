<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventFundRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventFundRequestController extends Controller
{
    // Hiển thị danh sách yêu cầu
    public function index()
    {
        $requests = EventFundRequest::with(['event', 'user'])->latest()->get();
        return view('admin.event_funds.requests.index', compact('requests'));
    }

    // Form tạo mới
    public function create()
    {
        $events = Event::all();
        $sourceTypes = ['school' => 'Quỹ nhà trường', 'sponsor' => 'Nhà tài trợ', 'club' => 'Quỹ CLB'];
        return view('admin.event_funds.requests.create', compact('events', 'sourceTypes'));
    }

    // Lưu dữ liệu
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'source_type' => 'required|in:school,sponsor,club',
            'amount_requested' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        EventFundRequest::create([
            'event_id' => $request->event_id,
            'source_type' => $request->source_type,
            'amount_requested' => $request->amount_requested,
            'note' => $request->note,
            'requested_by' => Auth::id(),
            'status' => 'pending', // mặc định
        ]);

        return redirect()->route('admin.event_fund_requests.index')
                         ->with('success', 'Đã thêm yêu cầu cấp kinh phí.');
    }

   public function show($id)
{
    // Eager load event và user để tránh N+1 query
    $request = EventFundRequest::with(['event', 'user'])->findOrFail($id);

    return view('admin.event_funds.requests.show', compact('request'));
}

    // Form chỉnh sửa
  // Show form edit
public function edit($id)
{
    $request = EventFundRequest::findOrFail($id);
    $events = Event::all(); // để dropdown chọn event nếu muốn
    return view('admin.event_funds.requests.edit', compact('request', 'events'));
}

// Update dữ liệu
public function update(Request $request, $id)
{
    $request->validate([
        'event_id' => 'required|exists:events,id',
        'amount_requested' => 'required|numeric|min:0',
        'status' => 'required|string',
    ]);

    $fundRequest = EventFundRequest::findOrFail($id);

    $fundRequest->update([
        'event_id' => $request->event_id,
        'amount_requested' => $request->amount_requested,
        'status' => $request->status,
        // nếu muốn admin có thể đổi người tạo:
        // 'created_by' => $request->created_by
    ]);

    return redirect()->route('admin.event_fund_requests.index')
                     ->with('success', 'Cập nhật yêu cầu cấp kinh phí thành công!');
}

function destroy($id)
    {
        $request = EventFundRequest::findOrFail($id);
        $request->delete();

        return redirect()->route('admin.event_fund_requests.index')
                         ->with('success', 'Đã xóa yêu cầu cấp kinh phí.');
    }

}
