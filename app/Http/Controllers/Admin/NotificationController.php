<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\User;
use App\Models\Event;
use App\Models\SentEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\GenericNotificationMail;
use App\Notifications\CustomNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function create()
    {
        return view('admin.notifications.create');
    }

    public function index(Request $request)
    {
        $sentEmails = SentEmail::with('user')->latest()->get();
        $inAppNotifications = DatabaseNotification::with('notifiable')->latest()->get();

        $grouped = [];

        // Xử lý email
        foreach ($sentEmails as $email) {
            $userId = $email->user?->id;
            $batchId = $email->batch_id ?? 'email-' . $email->id;
            $key = $batchId . '-' . ($userId ?? 'unknown-' . $email->id);

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'id' => $email->id,
                    'source' => 'email',
                    'batch_id' => $batchId,
                    'title' => $email->title,
                    'content' => $email->content,
                    'created_at' => $email->created_at,
                    'user' => $email->user?->ho_ten ?? $email->user?->email ?? '---',
                    'user_id' => $userId,
                    'channels' => [],
                ];
            }

            $grouped[$key]['channels']['Email'] = $email->status ?? '(không rõ)';
        }

        // Xử lý in-app
        foreach ($inAppNotifications as $n) {
            $userId = $n->notifiable?->id;
            $batchId = $n->batch_id ?? 'inapp-' . $n->id;
            $key = $batchId . '-' . ($userId ?? 'unknown-' . $n->id);

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'id' => $n->id,
                    'source' => 'notification',
                    'batch_id' => $batchId,
                    'title' => $n->data['title'] ?? '(Không có tiêu đề)',
                    'content' => $n->data['message'] ?? '(Không có nội dung)',
                    'created_at' => $n->created_at,
                    'user' => $n->notifiable?->ho_ten ?? $n->notifiable?->email ?? '---',
                    'user_id' => $userId,
                    'channels' => [],
                ];
            }

            $grouped[$key]['channels']['In-App'] = $n->status ?? '(không rõ)';
        }

        $activities = collect($grouped)
            ->sortByDesc('created_at')
            ->values();

        return view('admin.notifications.index', compact('activities'));
    }







    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $types = $request->input('types', []);

        if (empty($ids)) {
            return redirect()->route('admin.notifications.index')
                ->with('error', 'bạn chưa chọn thông báo để xóa');        }

        $deletedCount = 0;

        Log::info('Bulk delete request received', [
            'ids' => $ids,
            'types' => $types,
        ]);

        foreach ($ids as $id) {
            $idStr = (string) $id;
            $type = $types[$idStr] ?? 'notification';

            Log::info('Processing deletion', [
                'id' => $idStr,
                'type' => $type,
            ]);

            try {
                if ($type === 'email') {
                    $email = SentEmail::find((int) $idStr);
                    if ($email) {
                        $email->delete();
                        $deletedCount++;
                    } else {
                        Log::warning("Email not found: $idStr");
                    }
                } else {
                    $notification = DatabaseNotification::find($idStr);
                    if ($notification) {
                        $notification->delete();
                        $deletedCount++;
                    } else {
                        Log::warning("Notification not found: $idStr");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error deleting $type [$idStr]: " . $e->getMessage());
            }
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'đã xóa');    }
    public function resend(string $batchId, string $userId)
    {
        // Lấy user

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('admin.notifications.index')
                ->with('success', 'user không hợp lệ');        }

        $channelsToResend = [];

        // Kiểm tra email thất bại
        $email = SentEmail::where('batch_id', $batchId)
            ->where('user_id', $userId)
            ->where('status', 'failed')
            ->first();

        if ($email) {
            $channelsToResend[] = 'mail';
        }

        // Kiểm tra in-app thất bại (từ cột status, không phải data)
        $notification = DatabaseNotification::where('batch_id', $batchId)
            ->where('notifiable_id', $userId)
            ->where('status', 'failed')
            ->first();

        if ($notification) {
            $channelsToResend[] = 'database';
        }

        // Nếu không có kênh nào cần gửi lại
        if (empty($channelsToResend)) {
            return redirect()->route('admin.notifications.index')
                ->with('infor', 'Không có kênh nào cần gửi lại');        }

        // Lấy tiêu đề và nội dung từ email hoặc notification
        $title = $email?->title ?? $notification?->data['title'] ?? '(Không có tiêu đề)';
        $content = $email?->content ?? $notification?->data['message'] ?? '(Không có nội dung)';

        // Xác định kênh gửi
        $sendVia = count($channelsToResend) > 1 ? 'both' : $channelsToResend[0];

        // Gửi lại bằng job
        SendNotificationJob::dispatch($user->id, $title, $content, $sendVia, $batchId)
            ->onQueue('notifications');

        Log::info('Resending notification queued', [
            'user_id' => $user->id,
            'batch_id' => $batchId,
            'sendVia' => $sendVia
        ]);
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Thông báo thất bại đang được gửi lại...');
    }





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
        $role = $request->get('role'); // có thể null

        // === Nếu chọn vai trò "chủ nhiệm CLB" ===
        if ($role === 'club_manager') {
            // 1️⃣ Lấy tất cả manager_id (bỏ null, bỏ trùng)
            $managerIds = DB::table('clubs')
                ->whereNotNull('manager_id')
                ->pluck('manager_id')
                ->unique()
                ->toArray();

            if (empty($managerIds)) {
                return response()->json(['results' => []]);
            }

            // 2️⃣ Join sang members và users để lấy thông tin
            $managers = DB::table('members')
                ->join('users', 'members.user_id', '=', 'users.id')
                ->whereIn('members.id', $managerIds)
                ->where('users.status', 'active')
                ->select(
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.email'
                )
                ->get();

            // 3️⃣ Gom tên các CLB mà người đó là chủ nhiệm
            $clubsByManager = DB::table('clubs')
                ->whereIn('manager_id', $managerIds)
                ->select('manager_id', 'name')
                ->get()
                ->groupBy('manager_id');

            // 4️⃣ Kết hợp dữ liệu
            $results = $managers->map(function ($m) use ($clubsByManager) {
                $clubNames = $clubsByManager[$m->user_id] ?? collect();
                $clubList = $clubNames->pluck('name')->join(', ');
                return [
                    'id' => $m->user_id,
                    'text' => "{$m->user_name} ({$m->email}) - Chủ nhiệm của: {$clubList}"
                ];
            });

            return response()->json(['results' => $results]);
        }

        // === Nếu là thành viên bình thường ===
        if (!$clubId) {
            return response()->json(['results' => []]);
        }

        $query = DB::table('club_members')
            ->join('members', 'club_members.member_id', '=', 'members.id')
            ->join('users', 'members.user_id', '=', 'users.id')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('club_members.club_id', $clubId)
            ->where('users.status', 'active')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.email',
                'club_members.role',
                'clubs.name as club_name'
            );

        if (!empty($role)) {
            $query->where('club_members.role', $role);
        }

        $members = $query->get();

        $results = $members->map(fn($m) => [
            'id' => $m->user_id,
            'text' => "{$m->user_name} ({$m->email}) - {$m->role} tại {$m->club_name}"
        ]);

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

        $recipients = collect();

        if (!empty($data['users'])) {
            $recipients = User::whereIn('id', $data['users'])
                ->where('status', 'active')
                ->pluck('id'); // chỉ lấy id
        } else {
            switch ($data['target_type']) {
                case 'user':
                    $recipients = User::where('status', 'active')->pluck('id');
                    break;
                case 'club':
                    if (!empty($data['club_id'])) {
                        $recipients = \DB::table('club_members')
                            ->join('members', 'club_members.member_id', '=', 'members.id')
                            ->join('users', 'members.user_id', '=', 'users.id')
                            ->where('club_members.club_id', $data['club_id'])
                            ->where('users.status', 'active')
                            ->pluck('users.id');
                    }
                    break;
                case 'role':
                    if (!empty($data['role'])) {
                        $recipients = User::where('role', $data['role'])
                            ->where('status', 'active')
                            ->pluck('id');
                    }
                    break;
                case 'event':
                    if (!empty($data['event_id'])) {
                        $recipients = \DB::table('event_registrations')
                            ->where('event_id', $data['event_id'])
                            ->pluck('user_id');
                    }
                    break;
            }
        }

        if ($recipients->isEmpty()) {
            return redirect()->route('admin.notifications.store')
                ->with('error', 'không thấy người nhận hợp lệ');
        }

        $batchId = \Str::uuid()->toString();

        foreach ($recipients as $userId) {
            SendNotificationJob::dispatch(
                $userId,
                $data['title'],
                $data['content'],
                $data['send_via'],
                $batchId
            );
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Thông báo đang được gửi qua hàng đợi!');
    }

}
