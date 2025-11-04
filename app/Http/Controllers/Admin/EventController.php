<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\Club;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\Auth;
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

    return view('admin.events.index', compact('events', 'clubs'));
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
        'budget_estimated' => 'nullable|numeric|min:0',
        'budget_current' => 'nullable|numeric|min:0',
    ]);
    $validated['is_public'] = $request->has('is_public');
  // Nếu ngân sách hiện có chưa nhập, mặc định = 0
 $validated['budget_current'] = $validated['budget_current'] ?? 0;



    Event::create($validated);

    return redirect()->route('admin.events.index')
        ->with('success', 'Tạo sự kiện thành công!');
}
   
    public function show(Event $event)
{
    $event->load(['club', 'createdBy', 'approvalBy']);
    return view('admin.events.show', compact('event'));
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
        'budget_estimated' => 'nullable|numeric|min:0',
        'budget_current' => 'nullable|numeric|min:0',
        'budget_used' => 'nullable|numeric|min:0',
    ]);

    $validated['is_public'] = $request->has('is_public');

    $event->update($validated);

    return redirect()->route('admin.events.index', $event->id)
        ->with('success', 'Cập nhật sự kiện thành công!');
}

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Sự kiện đã được xóa thành công!');
    }

  public function approve(Event $event)
{
    if ($event->status !== 'pending') {
        return redirect()->back()->with('error', 'Sự kiện không ở trạng thái chờ duyệt!');
    }

    $event->update([
        'status' => 'approved',
        'approval_by' => auth()->id(), // ← đổi từ updated_by thành approval_by
        'approved_at' => now(),        // ← thêm nếu muốn lưu thời gian duyệt
    ]);

    return redirect()->route('admin.events.index')->with('success', 'Sự kiện đã được duyệt thành công!');
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
        $events = \App\Models\Event::where('club_id', $clubId)
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


}
