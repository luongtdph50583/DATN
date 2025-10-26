<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\GenericNotificationMail;
use App\Notifications\CustomNotification;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    public function create()
    {
        return view('admin.notifications.create');
    }

    // AJAX: lấy tất cả user (Select2) + filter theo role nếu có
    public function fetchUsers(Request $request)
    {
        $q = $request->get('q', '');
        $role = $request->get('role');

        $users = User::query()
            ->where('status', 'active')
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%"))
            ->when($role, fn($query) => $query->where('role', $role))
            ->limit(50)
            ->get(['id', 'name', 'email']);

        $results = $users->map(fn($u) => ['id' => $u->id, 'text' => "{$u->name} ({$u->email})"]);
        return response()->json(['results' => $results]);
    }

    // AJAX: lấy tất cả CLB
    public function fetchClubs()
    {
        $clubs = Club::select('id', 'name')->where('status', 'active')->get();
        return response()->json($clubs);
    }

    // AJAX: lấy thành viên của CLB từ bảng members
    public function fetchClubMembers(Request $request)
    {
        $clubId = $request->get('club_id');
        if (!$clubId) {
            return response()->json(['results' => []]);
        }

        $members = DB::table('club_members')
            ->join('members', 'club_members.member_id', '=', 'members.id')
            ->join('users', 'members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $clubId)
            ->where('users.status', 'active')
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        $results = $members->map(fn($u) => ['id' => $u->id, 'text' => "{$u->name} ({$u->email})"]);
        return response()->json(['results' => $results]);
    }


    // AJAX: lấy tất cả sự kiện
    public function fetchEvents()
    {
        $events = Event::select('id', 'name')->where('status', 'approved')->get();
        return response()->json($events);
    }

    // AJAX: lấy người tham gia sự kiện từ bảng event_registrations
    public function fetchEventMembers(Request $request)
    {
        $eventId = $request->get('event_id');
        if (!$eventId)
            return response()->json(['results' => []]);

        $participants = DB::table('event_registrations')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('event_registrations.event_id', $eventId)
            ->where('users.status', 'active')
            ->select('users.id', 'users.name', 'users.email')
            ->get();

        $results = $participants->map(fn($u) => ['id' => $u->id, 'text' => "{$u->name} ({$u->email})"]);
        return response()->json(['results' => $results]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_type' => 'required|in:user,club,role,event',
            'users' => 'nullable|array',
            'club_id' => 'nullable|exists:clubs,id',
            'role' => 'nullable|string',
            'event_id' => 'nullable|exists:events,id',
            'send_via' => 'required|in:database,mail,both',
        ]);

        // Lấy danh sách recipients (User model)
        $recipients = collect();

        switch ($data['target_type']) {
            case 'user':
                $recipients = User::whereIn('id', $data['users'] ?? [])
                    ->where('status', 'active')
                    ->get();
                break;

            case 'club':
                if (!empty($data['club_id'])) {
                    $recipients = \DB::table('club_members')
                        ->join('members', 'club_members.member_id', '=', 'members.id')
                        ->join('users', 'members.user_id', '=', 'users.id')
                        ->where('club_members.club_id', $data['club_id'])
                        ->where('users.status', 'active')
                        ->select('users.*')
                        ->get()
                        ->map(fn($u) => User::find($u->id))
                        ->filter(); // loại null
                }
                break;

            case 'role':
                if (!empty($data['role'])) {
                    $recipients = User::where('role', $data['role'])
                        ->where('status', 'active')
                        ->get();
                }
                break;

            case 'event':
                if (!empty($data['event_id'])) {
                    $recipients = \DB::table('event_registrations')
                        ->join('users', 'event_registrations.user_id', '=', 'users.id')
                        ->where('event_registrations.event_id', $data['event_id'])
                        ->where('users.status', 'active')
                        ->select('users.*')
                        ->get()
                        ->map(fn($u) => User::find($u->id))
                        ->filter();
                }
                break;
        }

        // Gửi thông báo
        foreach ($recipients as $user) {
            if (!$user)
                continue;

            if ($data['send_via'] === 'database') {
                $user->notify(new CustomNotification($data['title'], $data['content']));
            } elseif ($data['send_via'] === 'mail') {
                \Mail::to($user->email)
                    ->send(new GenericNotificationMail($data['title'], $data['content']));
            } else { // both
                $user->notify(new CustomNotification($data['title'], $data['content']));
                \Mail::to($user->email)
                    ->send(new GenericNotificationMail($data['title'], $data['content']));
            }
        }

        return redirect()
            ->route('admin.notifications.create')
            ->with('success', 'Thông báo đã gửi!');
    }

}
