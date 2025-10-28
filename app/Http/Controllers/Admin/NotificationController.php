<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Jobs\SendNotificationJob;
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

    public function index(Request $request)
    {
        $notifications = DB::table('notifications')
            ->select('id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at', 'created_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        // Lấy thông tin người dùng theo ID để hiển thị tên
        $userMap = User::whereIn('id', $notifications->pluck('notifiable_id')->unique())
            ->get()
            ->keyBy('id');

        return view('admin.notifications.index', compact('notifications', 'userMap'));
    }
    // AJAX: lấy tất cả user (Select2) + filter theo role nếu có
    public function fetchUsers(Request $request)
    {
        $q = $request->get('q', '');
        $role = $request->get('role');
        $all = $request->boolean('all');

        $query = User::query()->where('status', 'active');

        if (!$all) {
            $query->when($q, function ($query, $q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        // ❌ Không còn phân trang, lấy tất cả
        $users = $query->get(['id', 'name', 'email']);

        $results = $users->map(fn($u) => [
            'id' => $u->id,
            'text' => "{$u->name} ({$u->email})",
        ]);

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

        // dd($request->all());

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

        $recipients = collect();
        $userIds = $data['users'] ?? [];

        // ✅ Ưu tiên dùng danh sách users[] nếu có
        if (!empty($userIds)) {
            $recipients = User::whereIn('id', $userIds)
                ->where('status', 'active')
                ->get();
        } else {
            switch ($data['target_type']) {
                case 'user':
                    $recipients = User::where('status', 'active')->get();
                    break;

                case 'club':
                    if (!empty($data['club_id'])) {
                        $recipients = \DB::table('club_members')
                            ->join('members', 'club_members.member_id', '=', 'members.id')
                            ->join('users', 'members.user_id', '=', 'users.id')
                            ->where('club_members.club_id', $data['club_id'])
                            ->where('users.status', 'active')
                            ->select('users.id', 'users.name', 'users.email')
                            ->get()
                            ->map(fn($u) => new User((array) $u));
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
                            ->select('users.id', 'users.name', 'users.email')
                            ->get()
                            ->map(fn($u) => new User((array) $u));
                    }
                    break;
            }
        }

        if ($recipients->isEmpty()) {
            return back()->with('error', 'Không tìm thấy người nhận hợp lệ.');
        }

        foreach ($recipients as $user) {
            if (!$user || !$user->id)
                continue;
            SendNotificationJob::dispatch($user, $data['title'], $data['content'], $data['send_via']);
        }

        return redirect()
            ->route('admin.notifications.create')
            ->with('success', 'Thông báo đang được gửi qua hàng đợi!');
    }


}
