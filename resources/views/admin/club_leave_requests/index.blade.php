@extends('admin.layouts.app')

@section('title', 'Yêu cầu rời CLB')
@section('card-title', 'Danh sách yêu cầu rời CLB')

@section('card-body')
    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    {{-- Thanh tìm kiếm + lọc --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <form id="searchForm" onsubmit="return false;">
                <input type="text" id="keyword" class="form-control" placeholder="Tìm theo người gửi hoặc CLB...">
            </form>
        </div>
        <div class="col-md-3">
            <select id="filterStatus" class="form-select">
                <option value="">Tất cả trạng thái</option>
                <option value="pending">Chờ duyệt</option>
                <option value="approved">Đã duyệt</option>
                <option value="rejected">Từ chối</option>
            </select>
        </div>
    </div>

    {{-- Bảng --}}
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Người gửi</th>
                <th>CLB</th>
                <th>Ngày gửi</th>
                <th>Trạng thái</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody id="leaveTableBody">
            @foreach($requests as $index => $request)
                <tr>
                    <td>{{ $index + 1}}</td>
                    <td>{{ $request->user->name ?? '—' }}</td>
                    <td>{{ $request->club->name ?? '—' }}</td>
                    <td>{{ $request->requested_at ? $request->requested_at->format('d/m/Y') : '—' }}</td>
                    <td>
                        @if($request->status === 'pending')
                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @elseif($request->status === 'approved')
                        <span class="badge bg-success">Đã duyệt</span>
                        @elseif($request->status === 'rejected')
                        <span class="badge bg-danger">Từ chối</span>
                        @endif
                    </td>
                    <td class="text-center">
                            <form action="{{ route('admin.club_leave_requests.destroy', $request->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Bạn có chắc muốn xóa yêu cầu này không?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>

                        @if($request->status === 'pending')
                        <button class="btn btn-sm btn-primary" data-bs-toggle="offcanvas"
                            data-bs-target="#leaveRequestDetail{{ $request->id }}">
                            Xử lý
                        </button>

                        {{-- Offcanvas chi tiết --}}
                        <div class="offcanvas offcanvas-end border-0 shadow-lg rounded-4" tabindex="-1"
                            id="leaveRequestDetail{{ $request->id }}" aria-labelledby="leaveRequestLabel{{ $request->id }}"
                            style="width: 80%; background-color: #f8f9fa;">
                            <div class="offcanvas-header px-4 pt-4 pb-2 border-bottom">
                                <h5 class="offcanvas-title fw-semibold" id="leaveRequestLabel{{ $request->id }}">
                                    Chi tiết yêu cầu rời CLB
                                </h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body px-4 pb-4" id="leaveRequestContent{{ $request->id }}">
                                <div class="text-center text-muted py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <a href="{{ route('admin.club_leave_requests.show2', $request->id) }}"
                            class="btn btn-sm btn-outline-secondary">
                            Xem chi tiết
                        </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- JS xử lý --}}
    <script>
        // 🧩 Load chi tiết offcanvas
        document.querySelectorAll('[data-bs-toggle="offcanvas"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-bs-target').substring(1);
                const contentDiv = document.querySelector(`#${targetId} .offcanvas-body`);
                const requestId = targetId.replace('leaveRequestDetail', '');
                const url = "{{ url('admin/club-leave-requests') }}/" + requestId;

                contentDiv.innerHTML = `
                        <div class="text-center text-muted py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `;

                fetch(url)
                    .then(res => res.text())
                    .then(html => contentDiv.innerHTML = html)
                    .catch(err => contentDiv.innerHTML =
                        `<div class="alert alert-danger">Không tải được chi tiết: ${err}</div>`);
            });
        });

        // 🧩 Lọc + tìm kiếm AJAX

    </script>
@endsection

