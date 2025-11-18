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
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function create()
    {
        return view('admin.notifications.create');
    }

    public function index(Request $request)
    {
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->input('from_date'))->startOfDay() : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->input('to_date'))->endOfDay() : null;

        $sentEmails = SentEmail::with('user')
            ->when($fromDate, fn($q) => $q->where('created_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->where('created_at', '<=', $toDate))
            ->latest()
            ->get();

        $inAppNotifications = DatabaseNotification::with('notifiable')
            ->when($fromDate, fn($q) => $q->where('created_at', '>=', $fromDate))
            ->when($toDate, fn($q) => $q->where('created_at', '<=', $toDate))
            ->latest()
            ->get();

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

        $filters = [
            'from_date' => $fromDate?->format('Y-m-d'),
            'to_date' => $toDate?->format('Y-m-d'),
        ];

        return view('admin.notifications.index', compact('activities', 'filters'));
    }







    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $types = $request->input('types', []);

        if (empty($ids)) {
            return redirect()->route('admin.notifications.index')
                ->with('error', 'bạn chưa chọn thông báo để xóa');
        }

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
            ->with('success', 'đã xóa');
    }
    public function resend(string $batchId, string $userId)
    {
        // Lấy user

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('admin.notifications.index')
                ->with('success', 'user không hợp lệ');
        }

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
                ->with('infor', 'Không có kênh nào cần gửi lại');
        }

        // Lấy tiêu đề và nội dung từ email hoặc notification
        $title = $email?->title ?? $notification?->data['title'] ?? '(Không có tiêu đề)';
        $contentHtml = $email?->content ?? $notification?->data['message'] ?? '';
        $contentText = $notification?->data['message'] ?? $this->convertHtmlToPlain($contentHtml);

        // Xác định kênh gửi
        $sendVia = count($channelsToResend) > 1 ? 'both' : $channelsToResend[0];

        // Gửi lại bằng job
        SendNotificationJob::dispatch($user->id, $title, $contentHtml, $sendVia, $batchId, true, $contentText)
            ->onQueue('notifications');

        Log::info('Resending notification queued', [
            'user_id' => $user->id,
            'batch_id' => $batchId,
            'sendVia' => $sendVia
        ]);
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Thông báo thất bại đang được gửi lại...');
    }





    // public function fetchUsers(Request $request)
    // {
    //     $q = $request->get('q', '');
    //     $role = $request->get('role');
    //     $all = $request->boolean('all');

    //     $query = User::query()
    //         ->join('members', 'users.id', '=', 'members.user_id') // đảm bảo có member
    //         ->where('users.status', 'active');

    //     if (!$all) {
    //         $query->when($q, function ($query, $q) {
    //             $query->where(function ($subQuery) use ($q) {
    //                 $subQuery->where('users.name', 'like', "%{$q}%")
    //                     ->orWhere('users.email', 'like', "%{$q}%");
    //             });
    //         });
    //     }

    //     if ($role) {
    //         $query->where('users.role', $role);
    //     }

    //     $users = $query->select('users.id', 'users.name', 'users.email')->get();

    //     $results = $users->map(fn($u) => [
    //         'id' => $u->id,
    //         'text' => "{$u->name} ({$u->email})"
    //     ]);

    //     return response()->json(['results' => $results]);
    // }





    // AJAX: lấy tất cả CLB
    public function fetchClubs()
    {
        $clubs = Club::select('id', 'name')->where('status', 'active')->get();
        return response()->json($clubs);
    }

    // AJAX: lấy thành viên của CLB từ bảng members
    protected function getActiveUsers($query = null)
    {
        $query = $query ?? User::query();

        return $query->join('members', 'users.id', '=', 'members.user_id')
            ->where('users.status', 'active')
            ->select('users.id', 'users.name', 'users.email');
    }

    /**
     * Lấy danh sách user theo role, từ input q và all
     */
    public function fetchUsers(Request $request)
    {
        $ids = array_filter((array) $request->input('ids', []));
        $q = $request->get('q', '');
        $role = $request->get('role');
        $all = $request->boolean('all');

        $query = $this->getActiveUsers();

        if (!empty($ids)) {
            $query->whereIn('users.id', $ids);
        } else {
            if (!$all && $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('users.name', 'like', "%{$q}%")
                        ->orWhere('users.email', 'like', "%{$q}%");
                });
            }

            if ($role && !$this->isClubMemberRole($role)) {
                $query->where('users.role', $role);
            }
        }

        $users = $query->get();

        $results = $users->map(fn($u) => [
            'id' => $u->id,
            'text' => "{$u->name} ({$u->email})"
        ]);

        return response()->json(['results' => $results]);
    }

    /**
     * Helper: Build query lấy thành viên CLB theo club_id và role
     */
    protected function getClubMembersQuery($clubId = null, $role = null)
    {
        $query = DB::table('club_members')
            ->join('members', 'club_members.member_id', '=', 'members.id')
            ->join('users', 'members.user_id', '=', 'users.id')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('users.status', 'active');

        if ($clubId) {
            $query->where('club_members.club_id', $clubId);
        }

        if ($role) {
            $query->where('club_members.role', $role);
        }

        return $query->select(
            'users.id as user_id',
            'users.name as user_name',
            'users.email',
            'club_members.role',
            'clubs.name as club_name'
        );
    }

    /**
     * Lấy danh sách thành viên CLB
     * Nếu role = club_manager, lấy tất cả chủ nhiệm CLB
     */
    public function fetchClubMembers(Request $request)
    {
        $clubId = $request->get('club_id');
        $role = $request->get('role');
        $q = $request->get('q', '');
        $ids = array_filter((array) $request->input('ids', []));
        $all = $request->boolean('all');

        $query = DB::table('club_members')
            ->join('members', 'club_members.member_id', '=', 'members.id')
            ->join('users', 'members.user_id', '=', 'users.id')
            ->join('clubs', 'club_members.club_id', '=', 'clubs.id')
            ->where('users.status', 'active');

        if ($clubId) {
            $query->where('club_members.club_id', $clubId);
        }

        if ($role) {
            $query->where('club_members.role', $role);
        }

        if (!empty($ids)) {
            $query->whereIn('users.id', $ids);
        } elseif (!$all && $q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('users.name', 'like', "%{$q}%")
                    ->orWhere('users.email', 'like', "%{$q}%")
                    ->orWhere('clubs.name', 'like', "%{$q}%");
            });
        }

        $members = $query->select(
            'users.id as user_id',
            'users.name as user_name',
            'users.email',
            'club_members.role',
            'clubs.name as club_name'
        )->get();

        $results = $members->map(function ($m) {
            $roleLabel = $m->role ? Str::headline(str_replace('_', ' ', $m->role)) : 'Thành viên';
            return [
                'id' => $m->user_id,
                'text' => "{$m->user_name} ({$m->email}) - {$roleLabel} tại {$m->club_name}"
            ];
        });

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
        $ids = array_filter((array) $request->input('ids', []));
        $q = $request->get('q', '');
        $all = $request->boolean('all');

        if (!$eventId && empty($ids)) {
            return response()->json(['results' => []]);
        }

        $query = DB::table('event_registrations')
            ->join('users', 'event_registrations.user_id', '=', 'users.id')
            ->where('users.status', 'active');

        if ($eventId) {
            $query->where('event_registrations.event_id', $eventId);
        }

        if (!empty($ids)) {
            $query->whereIn('users.id', $ids);
        } elseif (!$all && $q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('users.name', 'like', "%{$q}%")
                    ->orWhere('users.email', 'like', "%{$q}%");
            });
        }

        $participants = $query->select('users.id', 'users.name', 'users.email')->get();

        $results = $participants->map(fn($u) => [
            'id' => $u->id,
            'text' => "{$u->name} ({$u->email})"
        ]);

        return response()->json(['results' => $results]);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content_html' => 'required|string',
            'target_type' => 'required|in:user,club,role,event',
            'users' => 'nullable|array',
            'club_id' => 'nullable|exists:clubs,id',
            'role' => 'nullable|string',
            'event_id' => 'nullable|exists:events,id',
            'send_via' => 'required|in:database,mail,both',
        ]);

        $htmlContent = $this->sanitizeHtml($data['content_html']);
        $plainContent = $this->convertHtmlToPlain($htmlContent);

        $recipients = collect();

        if (!empty($data['users'])) {
            $recipients = User::whereIn('id', $data['users'])
                ->where('status', 'active')
                ->pluck('id');
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
                        if ($this->isClubMemberRole($data['role'])) {
                            $recipients = \DB::table('club_members')
                                ->join('members', 'club_members.member_id', '=', 'members.id')
                                ->join('users', 'members.user_id', '=', 'users.id')
                                ->where('club_members.role', $data['role'])
                                ->where('users.status', 'active')
                                ->pluck('users.id');
                        } else {
                            $recipients = User::where('role', $data['role'])
                                ->where('status', 'active')
                                ->pluck('id');
                        }
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
            return redirect()->route('admin.notifications.create')
                ->with('error', 'Không tìm thấy người nhận hợp lệ')
                ->withInput();
        }

        $batchId = \Str::uuid()->toString();

        foreach ($recipients as $userId) {
            SendNotificationJob::dispatch(
                $userId,
                $data['title'],
                $htmlContent,
                $data['send_via'],
                $batchId,
                false,
                $plainContent
            );
        }

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Thông báo đang được gửi qua hàng đợi!');
    }

    protected function sanitizeHtml(string $html): string
    {
        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><blockquote>';
        $clean = strip_tags($html, $allowedTags);
        $clean = preg_replace('/<(\/?)\s*(strong|b|em|i|u|ul|ol|li|p|br|blockquote)([^>]*)>/i', '<$1$2>', $clean);
        $clean = str_ireplace(['<br>', '<br/>', '<br />'], '<br>', $clean);
        $clean = preg_replace('/(<br>\s*){3,}/', '<br><br>', $clean);
        $clean = preg_replace('/(<p>\s*){2,}/', '<p>', $clean);
        return trim($clean);
    }

    protected function convertHtmlToPlain(string $html): string
    {
        $map = [
            '<strong>' => '**',
            '</strong>' => '**',
            '<b>' => '**',
            '</b>' => '**',
            '<em>' => '*',
            '</em>' => '*',
            '<i>' => '*',
            '</i>' => '*',
            '<u>' => '_',
            '</u>' => '_',
            '<br>' => "\n",
            '</p>' => "\n\n",
            '<p>' => '',
        ];

        $normalized = str_ireplace(array_keys($map), array_values($map), $html);
        $normalized = strip_tags($normalized);
        $normalized = html_entity_decode($normalized, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalized = preg_replace("/[ \t]+/", ' ', $normalized);
        $normalized = preg_replace("/ *\n */", "\n", $normalized);
        $normalized = preg_replace("/\n{3,}/", "\n\n", $normalized);

        return trim($normalized);
    }

    protected function isClubMemberRole(string $role): bool
    {
        return in_array($role, [
            'club_manager',
            'deputy_manager',
            'secretary',
            'treasurer',
            'event_manager',
            'communication',
            'member',
        ], true);
    }
}
