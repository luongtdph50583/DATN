@extends('admin.layouts.app')

@section('title', 'Yêu cầu tham gia CLB')
@section('card-title', 'Danh sách yêu cầu tham gia CLB')

@section('card-body')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người gửi</th>
                <th>CLB</th>
                <th>Ngày gửi</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
                <tr>
                    <td>{{ $request->id }}</td>
                    <td>{{ $request->user->name ?? '—' }}</td>
                    <td>{{ $request->club->name ?? '—' }}</td>
                    <td>{{ $request->requested_at ? $request->requested_at->format('d/m/Y') : '—' }}</td>
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
                        @if($request->status === 'pending')
                            <button class="btn btn-primary btn-sm" data-bs-toggle="offcanvas"
                                data-bs-target="#clubJoinRequestDetail{{ $request->id }}">
                                Xem chi tiết
                            </button>

                            <!-- Offcanvas -->
                            <div class="offcanvas offcanvas-top" tabindex="-1" id="clubJoinRequestDetail{{ $request->id }}"
                                aria-labelledby="clubJoinRequestLabel{{ $request->id }}" style="height: 90vh; overflow-y: auto;">
                                <div class="offcanvas-header">
                                    <h5 class="offcanvas-title" id="clubJoinRequestLabel{{ $request->id }}">
                                        Chi tiết yêu cầu tham gia CLB
                                    </h5>
                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                        aria-label="Close"></button>
                                </div>
                                <div class="offcanvas-body" id="clubJoinRequestContent{{ $request->id }}">
                                    <div class="text-center text-muted">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted">Đã xử lý</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Không có yêu cầu nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
        document.querySelectorAll('[data-bs-toggle="offcanvas"]').forEach(btn => {
            btn.addEventListener('click', function () {
                const targetId = btn.getAttribute('data-bs-target').substring(1);
                const contentDiv = document.querySelector(`#${targetId} .offcanvas-body`);
                const requestId = targetId.replace('clubJoinRequestDetail', '');
                const url = `/admin/club-join-requests/${requestId}`;

                contentDiv.innerHTML = `
                        <div class="text-center text-muted">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    `;

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
    </script>
@endsection