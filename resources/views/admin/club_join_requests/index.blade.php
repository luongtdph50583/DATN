@extends('admin.layouts.app')

@section('title', 'Yêu cầu tham gia CLB')
@section('card-title', 'Danh sách yêu cầu tham gia CLB')
@section('card-header')
Danh sách
@endsection

@section('card-body')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- 🔍 Thanh tìm kiếm & lọc --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="keyword" class="form-control" placeholder="Tìm theo người gửi hoặc CLB...">
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

    {{-- 🧩 Bảng danh sách --}}
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>stt</th>
                <th>Người gửi</th>
                <th>CLB</th>
                <th>Ngày gửi</th>
                <th>Trạng thái</th>
                <th class="text-center">Hành động</th>
            </tr>
        </thead>
        <tbody id="requestTableBody">
            @forelse($requests as $index => $request)
                <tr>
                    <td>{{ $index + 1}}</td>
                    <td>{{ $request->user->name ?? '—' }}</td>
                    <td>{{ $request->club->name ?? '—' }}</td>
                    <td>{{ $request->requested_at ? $request->requested_at->format('d/m/Y') : '—' }}</td>
                  <td>
    @switch($request->status)
        @case('pending')
            <span class="badge bg-warning text-dark">Chờ duyệt</span>
            @break
        @case('scheduling_interview')
            <span class="badge bg-info text-dark">Đang lên lịch phỏng vấn</span>
            @break
        @case('interview')
            <span class="badge bg-primary">Đã có lịch phỏng vấn</span>
            @break
        @case('interview_completed')
            <span class="badge bg-secondary">Phỏng vấn xong, chờ duyệt</span>
            @break
        @case('approved')
            <span class="badge bg-success">Đã duyệt</span>
            @break
        @case('rejected')
            <span class="badge bg-danger">Từ chối</span>
            @break
        @case('cancelled')
            <span class="badge bg-dark">Đã hủy</span>
            @break
        @default
            <span class="badge bg-light text-dark">Không xác định</span>
    @endswitch
</td>

                    <td class="text-center">

                    @if($request->status === 'pending')
                        <button class="btn btn-sm btn-primary" data-bs-toggle="offcanvas"
                            data-bs-target="#clubRequestDetail{{ $request->id }}">
                            Xử lý yêu cầu
                        </button>
                        {{-- Offcanvas --}}
                        <div class="offcanvas offcanvas-end border-0 shadow-lg rounded-4" tabindex="-1"
                            id="clubRequestDetail{{ $request->id }}" style="width: 80%; background-color: #f8f9fa;">
                            <div class="offcanvas-header px-4 pt-4 pb-2 border-bottom">
                                <h5 class="offcanvas-title fw-semibold">Chi tiết yêu cầu CLB</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body px-4 pb-4">
                                <div class="text-center text-muted py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('admin.club_join_requests.show2', $request->id) }}"
                            class="btn btn-sm btn-secondary">
                            Xem chi tiết
                        </a>
                    @endif

                        <form action="{{ route('admin.club_join_requests.destroy', $request->id) }}" method="POST"
                            class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa yêu cầu này không?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                        </form>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">Không có yêu cầu nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endsection


    {{-- 🔧 Script xử lý AJAX --}}
    <script>
        
        document.addEventListener('DOMContentLoaded', function () {

            // 🧩 Bắt sự kiện mở Offcanvas
            document.querySelectorAll('[data-bs-toggle="offcanvas"]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const targetId = btn.getAttribute('data-bs-target').substring(1); // ví dụ: clubRequestDetail5
                    const contentDiv = document.querySelector(`#${targetId} .offcanvas-body`);
                    const requestId = targetId.replace('clubRequestDetail', ''); // lấy ID
                    const url = "{{ url('admin/club-join-requests') }}/" + requestId;

                    // Hiển thị hiệu ứng loading
                    contentDiv.innerHTML = `
                        <div class="text-center text-muted py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Đang tải dữ liệu...</p>
                        </div>
                    `;

                    // Gọi AJAX để tải chi tiết
                    fetch(url)
                        .then(res => res.text())
                        .then(html => {
                            contentDiv.innerHTML = html;
                        })
                        .catch(err => {
                            contentDiv.innerHTML = `
                                <div class="alert alert-danger">
                                    Không tải được chi tiết: ${err}
                                </div>
                            `;
                        });
                });
            });

            // 🔍 Tìm kiếm & lọc
            const keywordInput = document.getElementById('keyword');
            const filterSelect = document.getElementById('filterStatus');
            const tableBody = document.getElementById('requestTableBody');
            let typingTimer;

            const statusBadges = {
                pending: '<span class="badge bg-warning text-dark">Chờ duyệt</span>',
                approved: '<span class="badge bg-success">Đã duyệt</span>',
                rejected: '<span class="badge bg-danger">Từ chối</span>'
            };

            function renderTable(requests) {
                if (!requests.length) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                Không có yêu cầu nào.
                            </td>
                        </tr>`;
                    return;
                }

               tableBody.innerHTML = requests.map((r, index) => `
    <tr>
        <td>${index + 1}</td>

                        <td>${r.user}</td>
                        <td>${r.club}</td>
                        <td>${r.requested_at}</td>
                        <td>${statusBadges[r.status] ?? ''}</td>
                        <td class="text-center">
                            ${r.status === 'pending'
                        ? `
                                    <button class="btn btn-sm btn-primary"
                                        data-bs-toggle="offcanvas"
                                        data-bs-target="#clubRequestDetail${r.id}">
                                        Xử lý yêu cầu
                                    </button>

                                    <div class="offcanvas offcanvas-end border-0 shadow-lg rounded-4"
                                        tabindex="-1"
                                        id="clubRequestDetail${r.id}"
                                        style="width: 80%; background-color: #f8f9fa;">
                                        <div class="offcanvas-header px-4 pt-4 pb-2 border-bottom">
                                            <h5 class="offcanvas-title fw-semibold">Chi tiết yêu cầu CLB</h5>
                                            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                        </div>
                                        <div class="offcanvas-body px-4 pb-4">
                                            <div class="text-center text-muted py-5">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `
                        : `<a href="${r.show_url}" class="btn btn-sm btn-outline-secondary">Xem chi tiết</a>`
                    }
                        </td>
                    </tr>
                `).join('');

                // Gắn lại sự kiện cho nút mới render
                document.querySelectorAll('[data-bs-toggle="offcanvas"]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const targetId = btn.getAttribute('data-bs-target').substring(1);
                        const contentDiv = document.querySelector(`#${targetId} .offcanvas-body`);
                        const requestId = targetId.replace('clubRequestDetail', '');
                        const url = "{{ url('admin/club-join-requests') }}/" + requestId;

                        contentDiv.innerHTML = `
                            <div class="text-center text-muted py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Đang tải dữ liệu...</p>
                            </div>
                        `;

                        fetch(url)
                            .then(res => res.text())
                            .then(html => {
                                contentDiv.innerHTML = html;
                            })
                            .catch(err => {
                                contentDiv.innerHTML = `
                                    <div class="alert alert-danger">
                                        Không tải được chi tiết: ${err}
                                    </div>
                                `;
                            });
                    });
                });
            }

            function fetchRequests() {
                const keyword = keywordInput.value.trim();
                const status = filterSelect.value;
                const url = `{{ route('admin.club_join_requests.filter') }}?keyword=${keyword}&status=${status}`;

                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                `;

                fetch(url)
                    .then(res => res.json())
                    .then(data => renderTable(data.data))
                    .catch(() => {
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center text-danger">
                                    Lỗi tải dữ liệu.
                                </td>
                            </tr>`;
                    });
            }

            // ⌨️ Gõ tìm kiếm có delay 500ms
            keywordInput.addEventListener('keyup', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(fetchRequests, 500);
            });

            // 🎚️ Thay đổi filter
            filterSelect.addEventListener('change', fetchRequests);
        });
    </script>

