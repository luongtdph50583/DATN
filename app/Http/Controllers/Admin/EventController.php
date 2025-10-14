<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Club;
use App\Models\User;

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

    public function edit($id)
    {
        if (!Auth::check() || (Auth::check() && Auth::user()->role !== 'admin')) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }
        $clubs = Club::all();
             $users = User::where('role', 'admin')->orWhere('role', 'member')->get();
             return view('admin.events.edit', compact('event', 'clubs', 'users'));
    }

    public function update(Request $request, $id)
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

            $event->update($request->all());

             return redirect()->route('admin.events.index')->with('success', 'Sự kiện đã được cập nhật thành công.');

    }

    public function destroy($id)
    {
        if (!Auth::check() || (Auth::check() && Auth::user()->role !== 'admin')) {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập.');
        }

        $event = Event::findOrFail($id);
        $event->delete();

       $event->delete();

             return redirect()->route('admin.events.index')->with('success', 'Sự kiện đã được xóa thành công.');

    }
}