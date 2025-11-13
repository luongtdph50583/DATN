@php
    use Illuminate\Support\Str;

    $roleLabels = $roleLabels ?? [
        'club_manager' => 'Chủ nhiệm',
        'deputy_manager' => 'Phó chủ nhiệm',
        'secretary' => 'Thư ký',
        'treasurer' => 'Thủ quỹ',
        'event_manager' => 'Quản lý sự kiện',
        'communication' => 'Truyền thông',
        'member' => 'Thành viên',
    ];

    $startIndex = $startIndex ?? 1;

    if ($members instanceof \Illuminate\Pagination\LengthAwarePaginator) {
        $collection = collect($members->items());
        $startIndex = ($members->currentPage() - 1) * $members->perPage() + 1;
    } else {
        $collection = collect($members);
    }

    $collection = $collection
        ->filter(function ($item) {
            return ($item->role ?? null) === 'member';
        })
        ->values();
@endphp

@forelse($collection as $index => $member)
    @php
        $currentIndex = $startIndex + $loop->iteration - 1;
        $user = $member->member->user ?? null;
        $memberModel = $member->member ?? null;
        $status = $member->status ?? 'inactive';
    @endphp
    <tr data-name="{{ Str::lower($user->name ?? '') }}" data-code="{{ Str::lower($memberModel->student_code ?? '') }}"
        data-status="{{ $status }}">
        <td>{{ $currentIndex }}</td>
        <td>{{ $user->name ?? '—' }}</td>
        <td>{{ $memberModel->student_code ?? '—' }}</td>
        <td>{{ $roleLabels[$member->role] ?? $member->role }}</td>
        <td>{{
            $member->joined_at
            ? \Illuminate\Support\Carbon::parse($member->joined_at)->format('d/m/Y')
            : '—'
            }}</td>
        <td>
            <span class="badge bg-{{ $status === 'active' ? 'success' : ($status === 'banned' ? 'danger' : 'secondary') }}">
                {{ $status === 'active' ? 'Hoạt động' : ($status === 'banned' ? 'Cấm' : 'Ngưng') }}
            </span>
        </td>
        <td class="text-center">
            @if($memberModel)
                <a href="{{ route('admin.members.show', $memberModel->id) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-eye"></i> Chi tiết
                </a>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted">Không có thành viên phù hợp.</td>
    </tr>
@endforelse
