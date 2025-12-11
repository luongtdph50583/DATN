<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\EventAssignment;
use App\Http\Controllers\Controller;

class EventAssignmentController extends Controller
{
    public function index($club_id, $event_id)
    {
        $event = Event::findOrFail($event_id);
        $assignments = EventAssignment::with('user', 'assigner')
            ->where('event_id', $event_id)
            ->get();

        return view('event_assignments.index', compact('event', 'assignments'));
    }

    public function create($club_id, $event_id)
    {
        $event = Event::findOrFail($event_id);
        // Lấy danh sách thành viên CLB
        $members = User::whereHas('clubMembers', function ($q) use ($club_id) {
            $q->where('club_id', $club_id)->where('status', 'active');
        })->get();

        return view('event_assignments.create', compact('event', 'members'));
    }

    public function store(Request $request, $club_id, $event_id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'task_title' => 'required|string|max:255',
            'task_description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'in:low,medium,high',
        ]);

        EventAssignment::create([
            'event_id' => $event_id,
            'user_id' => $request->user_id,
            'assigned_by' => auth()->id(),
            'task_title' => $request->task_title,
            'task_description' => $request->task_description,
            'due_date' => $request->due_date,
            'priority' => $request->priority ?? 'medium',
        ]);

        return redirect()->route('club_manager.club.events.assignments.index', [$club_id, $event_id])
            ->with('success', 'Đã giao việc cho thành viên.');
    }
}

