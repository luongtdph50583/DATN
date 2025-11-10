@extends('admin.layouts.app')

@section('title', 'Danh sách yêu cầu cập nhật CLB')
@section('card-title', 'Danh sách yêu cầu cập nhật CLB')
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
                <input type="text" id="searchKeyword" class="form-control" placeholder="Tìm theo tên CLB hoặc người đề xuất">
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
                <th>Người đề xuất</th>
                <th>Ngày yêu cầu</th>
                <th>Trạng thái </th> <!-- thêm cột trạng thái request -->
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody id="clubRequestTableBody">
            @foreach($requests as $index => $request)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $request->club->name ?? $request->name ?? '—' }}</td>
                    <td>{{ $request->proposer->name ?? '—' }}</td>
                    <td>{{ $request->created_at->format('d/m/Y') }}</td>

                  
                    {{-- Trạng thái request --}}
                    <td>
                        @if($request->status === 'pending')
                            <span class="badge bg-warning text-dark">Chờ duyệt</span>
                        @elseif($request->status === 'approved')
                            <span class="badge bg-success">Đã duyệt</span>
                        @elseif($request->status === 'rejected')
                            <span class="badge bg-danger">Từ chối</span>
                        @endif
                    </td>

                    {{-- Hành động --}}
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
                                        Chi tiết yêu cầu cập nhật CLB
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
                            <a href="{{ route('admin.club_requests_update.show2', $request->id) }}"
                                class="btn btn-info btn-sm">Xem</a>
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
                    const url = "{{ url('admin/club-requests-update') }}/" + requestId;

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
