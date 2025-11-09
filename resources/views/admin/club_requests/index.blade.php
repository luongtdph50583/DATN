@extends('admin.layouts.app')

@section('title', 'Danh sách yêu cầu thành lập CLB')
@section('card-title', 'Danh sách yêu cầu CLB')
@section('card-header')
    Danh sách
@endsection

@section('card-body')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tìm kiếm và lọc -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <input type="text" id="searchKeyword" class="form-control"
                placeholder="Tìm theo tên CLB, người đề xuất hoặc lĩnh vực">
        </div>
        <div class="col-md-3">
            <select id="filterStatus" class="form-select">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending">Chờ duyệt</option>
                <option value="approved">Đã duyệt</option>
                <option value="rejected">Từ chối</option>
            </select>
        </div>
    </div>

    <!-- Bảng dữ liệu -->
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên CLB</th>
                <th>Lĩnh vực</th>
                <th>Người đề xuất</th>
                <th>Ngày yêu cầu</th>
                <th>Trạng thái</th>
                <th>Logo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody id="clubRequestTableBody">
            @foreach($requests as $index => $request)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $request->name }}</td>
                    <td>{{ $request->field ?? '—' }}</td>
                    <td>{{ $request->user->name ?? '—' }}</td>
                    <td>{{ $request->created_at->format('d/m/Y') }}</td>
                    <td>
                        @if($request->status === 'pending')
                            <span class="badge bg-warning">Chờ duyệt</span>
                        @elseif($request->status === 'approved')
                            <span class="badge bg-success">Đã duyệt</span>
                        @elseif($request->status === 'rejected')
                            <span class="badge bg-danger">Từ chối</span>
                        @endif
                    </td>

                    <td>
                        @if($request->logo)
                            <img src="{{ asset('storage/' . $request->logo) }}" alt="Logo CLB" style="height: 40px;">
                        @else
                            —
                        @endif
                    </td>
                    <td>

                        @if($request->status === 'pending')
                            <button class="btn btn-success btn-sm" type="button" data-bs-toggle="offcanvas"
                                data-bs-target="#clubRequestDetail{{ $request->id }}"
                                aria-controls="clubRequestDetail{{ $request->id }}">
                                Xử lý
                            </button>

                            <div class="offcanvas offcanvas-end" tabindex="-1" id="clubRequestDetail{{ $request->id }}"
                                aria-labelledby="clubRequestDetailLabel{{ $request->id }}" style="width: 80%;">
                                <div class="offcanvas-header">
                                    <h5 class="offcanvas-title" id="clubRequestDetailLabel{{ $request->id }}">
                                        Chi tiết yêu cầu CLB
                                    </h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body" id="clubRequestContent{{ $request->id }}">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('admin.club_requests.show2', $request->id) }}" class="btn btn-info btn-sm">Xem</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>


    <!-- JS -->
    <script>
        // Load chi tiết yêu cầu vào offcanvas
        document.querySelectorAll('[data-bs-toggle="offcanvas"]').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetId = btn.getAttribute('data-bs-target').substring(1);
                const contentDiv = document.querySelector(`#${targetId} .offcanvas-body`);
                const requestId = targetId.replace('clubRequestDetail', '');
                const url = "{{ url('admin/club-requests') }}/" + requestId;

                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        contentDiv.innerHTML = html;
                    })
                    .catch(err => {
                        contentDiv.innerHTML = `<div class="alert alert-danger">Không tải được chi tiết: ${err}</div>`;
                    });
            });
        });

        // Lọc realtime bằng JS
        const keywordInput = document.getElementById('searchKeyword');
        const statusSelect = document.getElementById('filterStatus');

        keywordInput.addEventListener('input', filterRequests);
        statusSelect.addEventListener('change', filterRequests);

        function filterRequests() {
            const keyword = keywordInput.value;
            const status = statusSelect.value;

            fetch(`{{ route('admin.club_requests.filter') }}?keyword=${encodeURIComponent(keyword)}&status=${status}`)
                .then(res => res.json())
                .then(data => {
                    const tbody = document.getElementById('clubRequestTableBody');
                    tbody.innerHTML = '';

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="text-center">Không tìm thấy kết quả phù hợp.</td></tr>';
                        return;
                    }

                    data.forEach(request => {
                        const statusLabel = {
                            pending: '<span class="badge bg-warning">Chờ duyệt</span>',
                            approved: '<span class="badge bg-success">Đã duyệt</span>',
                            rejected: '<span class="badge bg-danger">Từ chối</span>'
                        }[request.status] || '';

                        const action = request.status === 'pending'
                            ? `<button class="btn btn-success btn-sm" type="button" data-bs-toggle="offcanvas"
                                                data-bs-target="#clubRequestDetail${request.id}"
                                                aria-controls="clubRequestDetail${request.id}">Duyệt</button>`
                            : `<a href="/admin/club-requests/${request.id}/show2" class="btn btn-info btn-sm">Xem</a>`;

                        tbody.innerHTML += `
                                               <tr>
                <td>${request.id}</td>
                <td>${request.name ?? '—'}</td>
                <td>${request.field ?? '—'}</td>
                <td>${request.user?.name ?? '—'}</td>
                <td>${new Date(request.created_at).toLocaleDateString('vi-VN')}</td>
                <td>${statusLabel}</td>
                <td>
                    ${request.logo ? `<img src="${request.logo}" alt="Logo" style="height:40px;">` : '—'}
                </td>
                <td>${action}</td>
            </tr>

                                    `;
                    });
                })
                .catch(err => {
                    console.error('Lỗi khi lọc:', err);
                });
        }
    </script>
@endsection
