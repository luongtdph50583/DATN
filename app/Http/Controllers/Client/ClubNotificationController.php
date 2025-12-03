<?php

namespace App\Http\Controllers\Client;

use App\Models\Club;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendNotificationJobClient;

class ClubNotificationController extends Controller
{
    protected array $memberRoles = [
        'club_manager' => 'Chủ nhiệm',
        'deputy_manager' => 'Phó chủ nhiệm',
        'secretary' => 'Thư ký',
        'treasurer' => 'Thủ quỹ',
        'event_manager' => 'Sự kiện',
        'communication' => 'Truyền thông',
        'member' => 'Thành viên',
    ];

    public function create($club_id)
    {
        $club = Club::with('clubMembers.member.user')->findOrFail($club_id);
        $this->authorizeClubManager($club);

        $members = $club->clubMembers
            ->map(function ($membership) {
                $user = $membership->member?->user;
                if (!$user) {
                    return null;
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $membership->role,
                ];
            })
            ->filter()
            ->values();

        return view('client.pages.club.notifications', [
            'club' => $club,
            'memberRoles' => $this->memberRoles,
            'members' => $members,
        ]);
    }

    public function store(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content_html' => 'required|string',
            'send_via' => 'required|in:database,mail,both',
            'target' => 'required|in:all,role,custom',
            'role' => 'nullable|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer',
        ]);

        $recipientIds = $this->resolveRecipients(
            $club_id,
            $validated['target'],
            $validated['role'] ?? null,
            $validated['user_ids'] ?? []
        );

        if ($recipientIds->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'Không tìm thấy thành viên hợp lệ trong CLB.');
        }

        $htmlContent = $this->sanitizeHtml($validated['content_html']);
        $plainContent = $this->convertHtmlToPlain($htmlContent);
        $batchId = Str::uuid()->toString();

        foreach ($recipientIds as $userId) {
            SendNotificationJobClient::dispatch(
                $userId,
                $validated['title'],
                $htmlContent,
                $validated['send_via'],
                $batchId,
                false,
                $plainContent
            )->onQueue('notifications');
        }

        return redirect()
            ->route('club_manager.notifications.create', ['club_id' => $club_id])
            ->with('success', 'Thông báo đang được gửi đến .');
    }

    protected function resolveRecipients(int $clubId, string $target, ?string $role, array $userIds): Collection
    {
        $query = DB::table('club_members')
            ->join('members', 'club_members.member_id', '=', 'members.id')
            ->join('users', 'members.user_id', '=', 'users.id')
            ->where('club_members.club_id', $clubId)
            ->where('users.status', 'active');

        return match ($target) {
            'role' => $this->filterByRole($query, $role)->pluck('users.id'),
            'custom' => $this->filterByCustomUsers($query, $userIds)->pluck('users.id'),
            default => $query->pluck('users.id'),
        };
    }

    protected function filterByRole($query, ?string $role)
    {
        if ($role && array_key_exists($role, $this->memberRoles)) {
            return $query->where('club_members.role', $role);
        }

        return $query;
    }

    protected function filterByCustomUsers($query, array $userIds)
    {
        $ids = array_filter($userIds);
        if (empty($ids)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('users.id', $ids);
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

    private function authorizeClubManager($club)
    {
        $user = Auth::user();
        $managedClubs = $user->getManagedClubs();

        if (!$managedClubs->contains('id', $club->id)) {
            abort(403, 'Bạn không có quyền quản lý CLB này.');
        }
    }
}

