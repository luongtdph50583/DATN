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

                {{-- 🔹 Form lọc và tìm kiếm --}}
                {{-- <form method="GET" action="{{ route('admin.clubs.members', $club->id) }}" class="row mb-3 g-2"> --}}
                    <div class="col-md-3">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Chọn trạng thái --</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Ngưng hoạt động
                            </option>
                            <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Bị cấm</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="Tìm theo tên hoặc MSSV">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="membersTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tên</th>
                                <th>Mã SV</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Ngày tham gia</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $index = ($clubMembers->currentPage() - 1) * $clubMembers->perPage() + 1;
                                $roleLabels = [
                                    'club_manager' => 'Chủ nhiệm',
                                    'deputy_manager' => 'Phó chủ nhiệm',
                                    'secretary' => 'Thư ký',
                                    'treasurer' => 'Thủ quỹ',
                                    'event_manager' => 'Quản lý sự kiện',
                                    'communication' => 'Truyền thông',
                                    'member' => 'Thành viên',
                                ];
                            @endphp

                            @forelse($clubMembers as $member)
                                @if($member->role === 'member')
                                    <tr>
                                        <td>{{ $index++ }}</td>
                                        <td>{{ $member->member->user->name ?? '—' }}</td>
                                        <td>{{ $member->member->student_code ?? '—' }}</td>
                                        <td>{{ $roleLabels[$member->role] ?? $member->role }}</td>
                                        <td>
                                            <span class="badge bg-{{ $member->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ $member->status === 'active' ? 'Hoạt động' : 'Ngưng' }}
                                            </span>
                                        </td>
                                        <td>{{ $member->joined_at ? \Carbon\Carbon::parse($member->joined_at)->format('d/m/Y') : '—' }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ url('admin/members/' . $member->member->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Chi tiết
                                            </a>
                                            
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Chưa có thành viên nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $clubMembers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>



            <!-- Thông tin giảng viên đỡ đầu -->
            <div class="card mb-4 shadow-sm border-secondary">
                <div class="card-header bg-warning text-white fw-bold">Giảng viên đỡ đầu</div>
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
                        <em>Chưa có giảng viên đỡ đầu</em>
                    @endif
                </div>
            </div>
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
            const statusFilter = document.getElementById('statusFilter');
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('membersTableBody');

            function filterMembers() {
                const status = statusFilter.value.toLowerCase();
                const keyword = searchInput.value.toLowerCase();

                tableBody.querySelectorAll('tr').forEach(row => {
                    const name = row.getAttribute('data-name');
                    const code = row.getAttribute('data-code');
                    const rowStatus = row.getAttribute('data-status');

                    const matchStatus = status === '' || rowStatus === status;
                    const matchKeyword = name.includes(keyword) || code.includes(keyword);

                    row.style.display = matchStatus && matchKeyword ? '' : 'none';
                });
            }

            statusFilter.addEventListener('change', filterMembers);
            searchInput.addEventListener('input', filterMembers);
        });
        document.addEventListener('DOMContentLoaded', function () {
            // Xử lý sự kiện khi mở modal
            $('#removeMemberModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Nút kích hoạt modal
                var memberId = button.data('member-id'); // Lấy member_id
                var clubId = button.data('club-id'); // Lấy club_id

                // Cập nhật form action với club_id và member_id
                var form = $(this).find('form');
                var actionUrl = form.attr('action')
                    .replace('club_id', clubId)  // Thay 'club_id' bằng clubId
                    .replace('member_id', memberId); // Thay 'member_id' bằng memberId
                form.attr('action', actionUrl);
            });
        });
    </script>
@endpush
