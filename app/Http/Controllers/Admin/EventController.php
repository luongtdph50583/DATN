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
        $events = Event::with('club', 'createdBy')->orderBy('event_date', 'desc')->get();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $clubs = Club::all();
        return view('admin.events.create', compact('clubs'));
    }

    public function store(Request $request)
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

        Event::create($validated);

        return redirect()->route('admin.events.index')->with('success', 'Sự kiện đã được thêm thành công!');
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

        return redirect()->route('admin.events.index')->with('success', 'Sự kiện đã được cập nhật thành công!');
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
    }
}