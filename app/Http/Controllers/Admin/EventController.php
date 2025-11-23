<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\Club;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ClubMember;
use App\Models\EventFundRequest;
use App\Models\FundTransaction;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{

    public function index(Request $request)
{
    $query = Event::with(['createdBy', 'club', 'registrations'])->latest();

    if ($request->filled('search_name')) {
        $query->where('name', 'like', '%' . $request->search_name . '%');
    }

    if ($request->filled('club_id')) {
        $query->where('club_id', $request->club_id);
    }

    $events = $query->paginate(15);
    $clubs = Club::orderBy('name')->get();
    // TOP 10 CLB NHIỀU SỰ KIỆN NHẤT TRONG THÁNG NÀY
    $topClubs = Club::select('clubs.id', 'clubs.name')
    ->leftJoin('events', 'clubs.id', '=', 'events.club_id')
    ->whereMonth('events.created_at', now()->month)
    ->whereYear('events.created_at', now()->year)
    ->groupBy('clubs.id', 'clubs.name')
    ->orderByRaw('COUNT(events.id) DESC')
    ->withCount('events')
    ->limit(10)
    ->get();

    return view('admin.events.index', compact('events', 'clubs', 'topClubs'));
}

    public function create()
{
    $clubs = Club::orderBy('name')->get();
     $users = collect(); // rỗng, sẽ load động bằng AJAX

    return view('admin.events.create', compact('clubs', 'users'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'club_id' => 'required|exists:clubs,id',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'start_time' => 'required|date',
        'end_time' => 'required|date|after_or_equal:start_time',
        'location' => 'required|string|max:255',
        'max_participants' => 'nullable|integer|min:1',
        'is_public' => 'nullable|boolean',
        'status' => 'required|in:pending,approved,rejected',
        'created_by' => 'required|exists:users,id',
        'budget_items' => 'required|array|min:1',
        'budget_items.*.item_name' => 'required|string|max:255',
        'budget_items.*.estimated_cost' => 'required|numeric|min:0',
        'budget_items.*.type' => 'required|in:club_fund,school_fund,other',
    ]);

    $validated['is_public'] = $request->has('is_public');

    DB::transaction(function () use ($validated) {
        $event = Event::create([
            'club_id' => $validated['club_id'],
            'created_by' => $validated['created_by'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'location' => $validated['location'],
            'max_participants' => $validated['max_participants'],
            'is_public' => $validated['is_public'],
            'status' => $validated['status'],
        ]);

        // 2. Tạo chi tiết ngân sách
        foreach ($validated['budget_items'] as $index => $item) {
            $event->budgetItems()->create([
                'item_name' => $item['item_name'],
                'estimated_cost' => $item['estimated_cost'],
                'type' => $item['type'],
                'order' => $index,
            ]);
        }

        // 3. Nếu đã duyệt + có xin cấp trường → tạo yêu cầu quỹ
        if ($event->status === 'approved') {
            $schoolAmount = $event->budgetItems()->where('type', 'school_fund')->sum('estimated_cost');
            if ($schoolAmount > 0) {
                EventFundRequest::create([
                    'event_id' => $event->id,
                    'requested_by' => $validated['created_by'],
                    'amount_requested' => $schoolAmount,
                    'status' => 'pending_disbursement',
                    'note' => 'Tự động tạo từ chi tiết ngân sách',
                ]);
            }
        }
    });

    return redirect()
        ->route('admin.events.index')
        ->with('success', 'Tạo sự kiện + ngân sách chi tiết thành công!');
}

public function show(Event $event)
{
    // Eager load quan hệ cần thiết
    $event->load([
        'club',
        'createdBy',
        'approvalBy',
        'registrations.user',
        'funRequests.requestedBy',
        'funRequests.approvedBy',
        'media',
    ]);

    // Phân trang người tham gia (registrations)
    $registrations = $event->registrations()->with('user')->paginate(3);

    // Phân trang giao dịch quỹ (funRequests)
    $funRequests = $event->funRequests()->with(['approvedBy', 'requestedBy'])->paginate(2);

    // Lấy tất cả giao dịch CLB liên quan sự kiện này
    $clubTransactions = FundTransaction::where('club_id', $event->club_id)
        ->where('event_id', $event->id)
        ->with('creator') // eager load người tạo
        ->orderByDesc('created_at')
        ->get();

    return view('admin.events.show', compact('event', 'registrations', 'funRequests', 'clubTransactions'));
}



    public function edit(Event $event)
{
    $clubs = Club::orderBy('name')->get();
    $users = DB::table('users')
        ->join('club_members', 'users.id', '=', 'club_members.member_id')
        ->where('club_members.club_id', $event->club_id)
        ->where('club_members.role', 'admin')
        ->select('users.id', 'users.name', 'users.email')
        ->orderBy('users.name')
        ->get();

    $event->load(['club', 'createdBy', 'approvalBy']);

    return view('admin.events.edit', compact('event', 'clubs', 'users'));
}

public function update(Request $request, Event $event)
{
    $request->validate([
        'start_time' => 'required|date',
        'end_time' => 'required|date|after_or_equal:start_time',
        'status' => 'required|in:pending,approved,rejected',
        'media.*' => 'file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240', // 10MB
    ]);

    // Cập nhật các trường event
    $event->update($request->only(['start_time', 'end_time', 'status']));

    // Upload media mới
    if ($request->hasFile('media')) {
        foreach ($request->file('media') as $file) {
            $path = $file->store('events', 'public'); // lưu vào storage/app/public/events
            $event->media()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'uploaded_by' => auth()->id(),
            ]);
        }
    }

    return redirect()->route('admin.events.edit', $event)->with('success', 'Cập nhật sự kiện thành công!');
}



    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Sự kiện đã được xóa thành công!');
    }

 public function approve(Event $event)
{
    // 1️⃣ Chỉ duyệt khi sự kiện đang ở trạng thái chờ duyệt
    if ($event->status !== 'pending') {
        return redirect()->back()->with('error', 'Chỉ có thể duyệt sự kiện đang ở trạng thái chờ duyệt!');
    }

    DB::beginTransaction();
    try {
        // 2️⃣ Cập nhật trạng thái sự kiện
        $event->update([
            'status' => 'approved',
        ]);

        // 3️⃣ Nếu sự kiện có ngân sách yêu cầu thì tạo yêu cầu cấp kinh phí
        if ($event->budget_requested > 0 && !$event->fundRequest) {
            EventFundRequest::create([
                'event_id' => $event->id,
                'requested_by' => Auth::id(),
                'amount_requested' => $event->budget_requested,
                'status' => 'pending_disbursement',
                'note' => 'Tự động tạo khi sự kiện được duyệt.',
            ]);
        }

        DB::commit();
        return redirect()->back()->with('success', 'Duyệt sự kiện thành công và yêu cầu cấp kinh phí đã được tạo!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Đã xảy ra lỗi khi duyệt sự kiện: ' . $e->getMessage());
    }
}



    public function reject(Event $event)
    {
        if ($event->status !== 'pending') {
            return redirect()->back()->with('error', 'Sự kiện không ở trạng thái chờ duyệt!');
        }

        $event->update(['status' => 'rejected', 'updated_by' => auth()->id()]);
        return redirect()->route('admin.events.index')->with('success', 'Sự kiện đã bị từ chối!');
       $event->delete();

             return redirect()->route('admin.events.index')->with('success', 'Sự kiện đã được xóa thành công.');

    }

public function getEventsByClub($clubId)
{
    try {
        $events = Event::where('club_id', $clubId)
            ->select('id', 'name', 'start_time', 'end_time')
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}


public function getManagersByClub($clubId)
{
    try {
        $users = DB::table('users')
            ->join('club_members', 'users.id', '=', 'club_members.member_id') // đúng cột
            ->where('club_members.club_id', $clubId)
            ->where('club_members.role', 'admin') // role lưu trong club_members
            ->select('users.id', 'users.name', 'users.email')
            ->orderBy('users.name')
            ->get();

        return response()->json(['success' => true, 'data' => $users]);
    } catch (\Throwable $th) {
        return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
    }
}

public function getClubMembers($clubId)
{
    // ✅ Kiểm tra CLB tồn tại
    $club = Club::find($clubId);
    if (!$club) {
        return response()->json(['error' => 'Không tìm thấy CLB.'], 404);
    }

    // ✅ Lấy danh sách thành viên từ bảng club_members (có quan hệ với users)
    $members = ClubMember::where('club_id', $clubId)
        ->with('user:id,name,email') // chỉ lấy id, name, email
        ->get()
        ->map(function ($member) {
            return [
                'id' => $member->user->id,
                'name' => $member->user->name,
                'email' => $member->user->email,
            ];
        });

    return response()->json($members);
}
public function softDelete(Request $request, Event $event)
{
    $request->validate([
        'delete_reason' => 'required|string|max:1000'
    ]);

    $event->update([
        'deleted_by' => auth()->id(),
        'delete_reason' => $request->delete_reason
    ]);

    $event->delete(); // Soft delete

    return redirect()->route('admin.events.index')
        ->with('success', 'Đã xóa mềm sự kiện thành công! Đã lưu lý do.');
}
public function deleted()
{
    $events = Event::onlyTrashed()
        ->with(['club', 'createdBy', 'deletedBy'])
        ->latest('deleted_at')
        ->paginate(15);

    return view('admin.events.deleted', compact('events'));
}
public function restore($id)
{
    // ✅ Lấy bản ghi kể cả đã bị xóa mềm
    $event = Event::withTrashed()->findOrFail($id);

    if (!$event->trashed()) {
        return redirect()
            ->route('admin.events.deleted')
            ->with('error', 'Sự kiện này chưa bị xóa hoặc đã được khôi phục!');
    }

    // ✅ Thực hiện khôi phục
    $event->restore();

    // ✅ Xóa lý do xóa (nếu có)
    $event->update(['delete_reason' => null]);

    // ✅ Chuyển hướng về trang danh sách chính
    return redirect()
        ->route('admin.events.index')
        ->with('success', "🎉 Đã khôi phục thành công sự kiện: <strong>{$event->name}</strong>");
}
public function editBudget(Event $event)
{
    $event->load('budgetItems');
    return view('admin.events.budget_edit', compact('event'));
}

public function updateBudget(Request $request, Event $event)
{
    $request->validate([
        'items' => 'required|array',
        'items.*.item_name' => 'required|string|max:255',
        'items.*.estimated_cost' => 'required|numeric|min:0',
        'items.*.type' => 'required|in:club_fund,school_fund,other',
    ]);

    // Xóa cũ, thêm mới (đơn giản & an toàn)
    $event->budgetItems()->delete();

    foreach ($request->items as $index => $item) {
        $event->budgetItems()->create([
            'item_name'       => $item['item_name'],
            'description'     => $item['description'] ?? null,
            'estimated_cost'  => $item['estimated_cost'],
            'type'            => $item['type'],
            'order'           => $index,
        ]);
    }

    return redirect()
        ->route('admin.events.show', $event)
        ->with('success', 'Cập nhật ngân sách chi tiết thành công!');
}

}
