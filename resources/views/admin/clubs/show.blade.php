@extends('admin.layouts.app')

@section('title', 'Chi tiết CLB')
@section('card-title', 'Chi tiết CLB: ' . $club->name)

@section('card-body')
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="container-fluid py-4">

            <!-- Thông tin CLB -->
            <div class="card mb-4 shadow-sm border-secondary">
                <div class="card-header bg-primary text-white fw-bold">Thông tin Câu lạc bộ</div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($club->logo)
                            <img src="{{ Storage::url($club->logo) }}" class="rounded me-3"
                                style="width:80px;height:80px;object-fit:cover;">
                        @else
                            <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center me-3"
                                style="width:80px;height:80px;font-size:32px;">
                                {{ strtoupper(substr($club->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h1 class="h4 mb-1">{{ $club->name }}</h1>
                            <p class="text-muted small mb-0">{{ $club->field ?? '—' }}</p>
                        </div>
                        <div>

                            <p class="text-primary small fst-italic mb-0">{{ $club->slogan ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Email:</strong> {{ $club->email ?? '—' }}</div>
                        <div class="col-md-4"><strong>Điện thoại:</strong> {{ $club->phone ?? '—' }}</div>
                        <div class="col-md-4"><strong>Giới hạn thành viên:</strong> {{ $club->member_limit ?? '—' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Ngày thành lập:</strong>
                            {{ $club->founded_at ? \Carbon\Carbon::parse($club->founded_at)->format('d/m/Y') : '—' }}</div>
                        <div class="col-md-8"><strong>Địa điểm:</strong> {{ $club->location ?? '—' }}</div>
                    </div>
                    <p><strong>Mô tả:</strong></p>
                    <div class="border rounded p-2 bg-light">
                        {!! $club->description ?? '<em>Chưa có mô tả</em>' !!}
                    </div>
                    <p class="mt-3"><strong>Nội quy:</strong></p>
                    <div class="border rounded p-2 bg-light">
                        {!! $club->rules ?? '<em>Chưa có nội quy</em>' !!}
                    </div>
                </div>
            </div>

            <!-- Ban quản lý CLB -->
            <div class="card mb-4 shadow-sm border-secondary">
                <div class="card-header bg-success text-white fw-bold">Ban quản lý CLB</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tên</th>
                                    <th>Mã SV</th>
                                    <th>Vai trò</th>
                                    <th>Ngày bổ nhiệm</th>
                                    <th>Trạng thái</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
    $index = 1;
    $roleLabels = [
        'club_manager' => 'Chủ nhiệm',
        'deputy_manager' => 'Phó chủ nhiệm',
        'secretary' => 'Thư ký',
        'treasurer' => 'Thủ quỹ',
        'event_manager' => 'Quản lý sự kiện',
        'communication' => 'Truyền thông',
        'member' => 'Thành viên',
    ];

    $rolePriority = [
        'club_manager' => 1,
        'deputy_manager' => 2,
        'secretary' => 3,
        'treasurer' => 4,
        'event_manager' => 5,
        'communication' => 6,
    ];

    // Lấy mảng items từ paginator hoặc collection
    $membersCollection = $clubMembers instanceof \Illuminate\Pagination\LengthAwarePaginator
        ? collect($clubMembers->items())
        : collect($clubMembers);

    // Lọc ban quản lý và sắp xếp theo vai trò
    $sortedClubMembers = $membersCollection
        ->filter(fn($m) => in_array($m->role, array_keys($rolePriority)))
        ->sortBy(fn($m) => $rolePriority[$m->role] ?? 99);
                                @endphp

                                @forelse($sortedClubMembers as $member)
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $member->member->user->name ?? '—' }}</td>
                                        <td>{{ $member->member->student_code ?? '—' }}</td>
                                        <td>{{ $roleLabels[$member->role] ?? $member->role }}</td>
                                        <td>{{ $member->appointed_at ? \Carbon\Carbon::parse($member->appointed_at)->format('d/m/Y') : '—' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $member->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ $member->status === 'active' ? 'Hoạt động' : 'Ngưng' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if(!empty($member->member->id))
                                                <a href="{{ url('admin/members/' . $member->member->id) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Chi tiết
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Chưa có ban quản lý.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            <!-- Danh sách thành viên -->
        <div class="card mb-4 shadow-sm border-secondary">
            <div class="card-header bg-secondary text-white fw-bold">Danh sách thành viên CLB</div>
            <div class="card-body">

                <div class="row g-2 align-items-center mb-3" id="memberFilters">
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="fas fa-search text-secondary"></i></span>
                            <input
                                type="text"
                                id="memberSearchInput"
                                class="form-control"
                                placeholder="Tìm theo tên hoặc MSSV"
                                autocomplete="off"
                            >
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="memberStatusFilter" class="form-select form-select-sm">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="active">Hoạt động</option>
                            <option value="inactive">Ngưng hoạt động</option>
                            <option value="banned">Bị cấm</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div id="memberFilterFeedback" class="text-muted small"></div>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" id="memberResetFilter" class="btn btn-outline-secondary btn-sm w-100">
                            Đặt lại
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    @php
                        $memberRoleLabels = [
                            'club_manager' => 'Chủ nhiệm',
                            'deputy_manager' => 'Phó chủ nhiệm',
                            'secretary' => 'Thư ký',
                            'treasurer' => 'Thủ quỹ',
                            'event_manager' => 'Quản lý sự kiện',
                            'communication' => 'Truyền thông',
                            'member' => 'Thành viên',
                        ];
                        $membersStartIndex = ($clubMembers->currentPage() - 1) * $clubMembers->perPage() + 1;
                    @endphp
                    <table class="table table-bordered table-striped" id="membersTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tên</th>
                                <th>Mã SV</th>
                                <th>Vai trò</th>
                                <th>Ngày tham gia</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody id="membersTableBody">
                            @include('admin.clubs.partials.members_rows', [
                                'members' => $clubMembers,
                                'startIndex' => $membersStartIndex,
                                'roleLabels' => $memberRoleLabels,
                            ])
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                <div class="d-flex justify-content-center mt-3" id="membersPagination">
                    {{ $clubMembers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>



            <!-- Thông tin giảng viên đỡ đầu -->
            {{-- <div class="card mb-4 shadow-sm border-secondary">
                <div class="card-header bg-warning text-white fw-bold">Giảng viên phụ trách</div>
                <div class="card-body">
                    @if($club->advisorFaculty)
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Tên:</strong> {{ $club->advisorFaculty->user->name ?? '—' }}</div>
                            <div class="col-md-4"><strong>Email:</strong> {{ $club->advisorFaculty->user->email ?? '—' }}</div>
                            <div class="col-md-4"><strong>Mã NV:</strong> {{ $club->advisorFaculty->employee_code ?? '—' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Chức vụ:</strong> {{ $club->advisorFaculty->position ?? '—' }}</div>
                            <div class="col-md-4"><strong>Phòng ban:</strong> {{ $club->advisorFaculty->department ?? '—' }}</div>
                            <div class="col-md-4"><strong>Văn phòng:</strong> {{ $club->advisorFaculty->office_location ?? '—' }}
                            </div>
                        </div>
                    @else
                        <em>Chưa có giảng viên phụ trách</em>
                    @endif
                </div>
            </div> --}}
            {{-- Thông tin tổng số bài viết & sự kiện --}}
            <div class="card mb-4 shadow-sm border-primary">
                <div class="card-header bg-info text-white fw-bold">Thông tin hoạt động CLB</div>
                <div class="card-body">

                    {{-- Tổng số bài viết & sự kiện --}}
                    <div class="row text-center mb-4">
                        <div class="col-md-6">
                            <div class="h5">Tổng bài viết</div>
                            <div class="display-6 fw-bold">{{ $club->total_posts }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="h5">Tổng sự kiện</div>
                            <div class="display-6 fw-bold">{{ $club->total_events }}</div>
                        </div>
                    </div>

                    {{-- Bảng bài viết & sự kiện ngang nhau --}}
                    <div class="row">
                        {{-- Bảng bài viết --}}
                        <div class="col-md-6">
                            <h6>Bài viết nổi bật gần đây</h6>
                            @if($featuredPosts->count())
                                <table class="table table-striped table-bordered mb-4">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Tiêu đề</th>
                                            <th>Ngày tạo</th>
                                            <th>Visibility</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($featuredPosts as $index => $post)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $post->title }}</td>
                                                <td>{{ optional($post->created_at)->format('d/m/Y') ?? '—' }}</td>
                                                <td>{{ $post->visibility === 'public' ? 'Công khai' : 'Nội bộ' }}</td>
                                                <td>
                                                    <a href="{{ route('admin.posts.show', $post->id) }}" class="btn btn-sm btn-primary">
                                                        Xem chi tiết
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <em>Chưa có bài viết nổi bật</em>
                            @endif
                        </div>

                        {{-- Bảng sự kiện --}}
                        <div class="col-md-6">
                            <h6>Sự kiện sắp diễn ra</h6>
                            @if($upcomingEvents->count())
                                <table class="table table-striped table-bordered mb-4">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Tên sự kiện</th>
                                            <th>Thời gian bắt đầu</th>
                                            <th>Địa điểm</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($upcomingEvents as $index => $event)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $event->name }}</td>
                                                <td>{{ optional($event->start_time)->format('d/m/Y H:i') ?? '—' }}</td>
                                                <td>{{ $event->location ?? '—' }}</td>
                                                <td>
                                                    <a href="{{ route('admin.events.show', $event->id) }}"
                                                        class="btn btn-sm btn-primary">
                                                        Xem chi tiết
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <em>Chưa có sự kiện sắp diễn ra</em>
                            @endif
                        </div>
                    </div>
                </div>
            </div>






        </div>
@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('memberSearchInput');
            const statusFilter = document.getElementById('memberStatusFilter');
            const resetButton = document.getElementById('memberResetFilter');
            const tableBody = document.getElementById('membersTableBody');
            const paginationWrapper = document.getElementById('membersPagination');
            const feedback = document.getElementById('memberFilterFeedback');
            const endpoint = @json(route('admin.clubs.members.filter', $club->id));

            const initialState = {
                tableHtml: tableBody.innerHTML,
                paginationHtml: paginationWrapper ? paginationWrapper.innerHTML : '',
            };

            const updateFeedback = (count = null) => {
                if (!feedback) {
                    return;
                }
                if (count === null) {
                    feedback.textContent = '';
                } else {
                    feedback.textContent = `Tìm thấy ${count} thành viên`;
                }
            };

            const restoreInitial = () => {
                tableBody.innerHTML = initialState.tableHtml;
                if (paginationWrapper) {
                    paginationWrapper.innerHTML = initialState.paginationHtml;
                    paginationWrapper.style.display = '';
                }
                updateFeedback(null);
            };

            const isFiltered = () => (
                searchInput.value.trim() !== '' || statusFilter.value.trim() !== ''
            );

            let debounceTimer;
            let abortController = null;

            const fetchMembers = () => {
                if (!isFiltered()) {
                    if (abortController) {
                        abortController.abort();
                        abortController = null;
                    }
                    restoreInitial();
                    return;
                }

                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                const params = new URLSearchParams();
                if (searchInput.value.trim()) {
                    params.append('keyword', searchInput.value.trim());
                }
                if (statusFilter.value.trim()) {
                    params.append('status', statusFilter.value.trim());
                }

                tableBody.classList.add('opacity-50');

                fetch(`${endpoint}?${params.toString()}`, { signal: abortController.signal })
                    .then(response => response.json())
                    .then(data => {
                        tableBody.innerHTML = data.html;
                        updateFeedback(data.count ?? 0);
                        if (paginationWrapper) {
                            paginationWrapper.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        if (error.name !== 'AbortError') {
                            console.error('Lỗi lọc thành viên CLB:', error);
                        }
                    })
                    .finally(() => {
                        tableBody.classList.remove('opacity-50');
                    });
            };

            const debounceFetch = () => {
                if (debounceTimer) {
                    clearTimeout(debounceTimer);
                }
                debounceTimer = setTimeout(fetchMembers, 300);
            };

            searchInput.addEventListener('input', debounceFetch);
            statusFilter.addEventListener('change', fetchMembers);
            resetButton.addEventListener('click', () => {
                searchInput.value = '';
                statusFilter.value = '';
                restoreInitial();
            });

            updateFeedback(null);

            // Xử lý modal xóa thành viên (nếu sử dụng)
            const removeMemberModal = document.getElementById('removeMemberModal');
            if (removeMemberModal) {
                removeMemberModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    if (!button) return;
                    const memberId = button.getAttribute('data-member-id');
                    const clubId = button.getAttribute('data-club-id');
                    const form = removeMemberModal.querySelector('form');
                    if (!form) return;
                    const actionTemplate = form.dataset.actionTemplate || form.getAttribute('action');
                    if (!form.dataset.actionTemplate) {
                        form.dataset.actionTemplate = actionTemplate;
                    }
                    const updatedAction = form.dataset.actionTemplate
                        .replace('club_id', clubId)
                        .replace('member_id', memberId);
                    form.setAttribute('action', updatedAction);
                });
            }
        });
    </script>
@endpush
