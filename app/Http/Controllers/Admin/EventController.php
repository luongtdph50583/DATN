<?php

namespace App\Http\Controllers\Admin;

use App\Models\Event;
use App\Models\Club;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventController extends Controller
{

    public function index()
    {
        $events = [];
        try {
            $events = Event::orderBy('event_date', 'desc')->get();
        } catch (\Exception $e) {
            \Log::error('Lỗi truy vấn events: ' . $e->getMessage());
        }
         $events = Event::with(['club', 'createdBy'])->get();
             return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        if (!Auth::check() || (Auth::check() && Auth::user()->role !== 'admin')) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }
        $clubs = Club::all();
             $users = User::where('role', 'admin')->orWhere('role', 'member')->get();
             return view('admin.events.create', compact('clubs', 'users'));

    }

    public function store(Request $request)
    {
        if (!Auth::check() || (Auth::check() && Auth::user()->role !== 'admin')) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }

         $request->validate([
                 'club_id' => 'required|exists:clubs,id',
                 'name' => 'required|string|max:255',
                 'description' => 'nullable|string',
                 'event_date' => 'required|date',
                 'location' => 'required|string|max:255',
                 'status' => 'required|in:pending,approved,rejected',
                 'created_by' => 'required|exists:users,id',
             ]);

             Event::create($request->all());

             return redirect()->route('admin.events.index')->with('success', 'Sự kiện đã được tạo thành công.');
         
    }
    // chi tiết sự kiện
    public function show(Event $event)
         {
             if (!Auth::check() || (Auth::check() && Auth::user()->role !== 'admin')) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }
             $event->load(['club', 'createdBy']);
             return view('admin.events.show', compact('event'));
         }

    public function show(Event $event)
    {
        $registrations = $event->registrations()->with('user')->get();
        $totalRegistrations = $event->registrations()->count();
        return view('admin.events.show', compact('event', 'registrations', 'totalRegistrations'));
    }

    public function edit(Event $event)
    {
        $clubs = Club::all();
        return view('admin.events.edit', compact('event', 'clubs'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'club_id' => 'nullable|exists:clubs,id',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $validated['created_by'] = auth()->id();

        $event->update($validated);

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

        $event->update(['status' => 'approved', 'updated_by' => auth()->id()]);
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
}
