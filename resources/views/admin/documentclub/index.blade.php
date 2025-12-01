@extends('admin.layouts.app')

@section('title', 'Quản lý Tài liệu CLB')

@section('card-header')
    Tài liệu CLB
@endsection

@push('scripts')
    <script>
        function rejectDocument(docId, docTitle) {
            const reason = prompt('Nhập lý do từ chối tài liệu "' + docTitle + '":');

            if (reason === null || reason.trim() === '') {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("admin/documentclub") }}/' + docId + '/reject';
            form.classList.add('d-none');

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason.trim();
            form.appendChild(reasonInput);

            document.body.appendChild(form);
            form.submit();
        }

        function deleteDocument(docId, docTitle) {
            const reason = prompt('Nhập lý do xóa tài liệu "' + docTitle + '":');

            if (reason === null || reason.trim() === '') {
                return;
            }

            if (!confirm('Bạn có chắc chắn muốn xóa tài liệu "' + docTitle + '"?')) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("admin/documentclub") }}/' + docId;
            form.classList.add('d-none');

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);

            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason.trim();
            form.appendChild(reasonInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush

@section('card-body')
    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <h4 class="mb-0">Danh sách tài liệu theo CLB & Tag</h4>
        <a href="{{ route('admin.documentclub.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Thêm tài liệu
        </a>
    </div>

    <form method="GET" action="{{ route('admin.documentclub.index') }}" class="row g-3 align-items-end mb-4">
        <div class="col-md-5 col-lg-4">
            <label for="searchInput" class="form-label fw-semibold">Từ khóa</label>
            <input type="text" name="search" id="searchInput" value="{{ $search }}" class="form-control"
                placeholder="Tìm theo tên file, người tải, tiêu đề...">
        </div>
        <div class="col-md-4 col-lg-3">
            <label for="typeFilter" class="form-label fw-semibold">Loại tài liệu</label>
            <select name="type" id="typeFilter" class="form-select">
                @foreach($typeOptions as $value => $label)
                    <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 col-lg-2 d-grid">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search me-1"></i> Lọc
            </button>
        </div>
        <div class="col-md-3 col-lg-2 d-grid">
            <a href="{{ route('admin.documentclub.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại
            </a>
        </div>
    </form>

    {{-- Container hiển thị tài liệu --}}
    <div>
        @forelse($documentsByClub as $clubName => $docsByTag)
            <div class="card mb-4">
                <div class="card-header bg-light"><strong>{{ $clubName }}</strong></div>
                <div class="card-body p-0">
                    @forelse($docsByTag as $tag => $docs)
                        <div class="mb-3">
                            <div class="mb-1"><strong>Tag:</strong> {{ $tag ?: 'Không có tag' }} ({{ count($docs) }} tài liệu)</div>
                            @if(count($docs) === 0)
                                <p class="text-muted p-2">Không có tài liệu</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th>Tiêu đề</th>
                                                <th>Người tải lên</th>
                                                <th>Trạng thái</th>
                                                <th>Loại</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($docs as $doc)
                                                <tr>
                                                    <td>{{ $doc->title }}</td>
                                                    <td>{{ $doc->uploader->name ?? '-' }}</td>
                                                    @php
                                                        $statusLabels = [
                                                            'pending' => 'Đang chờ duyệt',
                                                            'approved' => 'Đã phê duyệt',
                                                            'rejected' => 'Từ chối',
                                                        ];
                                                    @endphp
                                                    <td>{{ $statusLabels[$doc->status] ?? ucfirst($doc->status) }}</td>
                                                    <td>{{ strtoupper($doc->file_type) }}</td>
                                                    <td class="text-nowrap">
                                                        {{-- Xem --}}
                                                        <a href="{{ route('admin.documentclub.show', $doc->id) }}"
                                                            class="btn btn-warning btn-sm me-1" title="Xem">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        {{-- Nếu đang chờ duyệt --}}
                                                        @if($doc->status === 'pending')
                                                            {{-- Duyệt --}}
                                                            <form action="{{ route('admin.documentclub.approve', $doc->id) }}" method="POST"
                                                                class="d-inline me-1">
                                                                @csrf
                                                                <button type="submit" class="btn btn-success btn-sm" title="Duyệt">
                                                                    <i class="fas fa-check-circle"></i>
                                                                </button>
                                                            </form>

                                                            {{-- Từ chối --}}
                                                            <button type="button" class="btn btn-danger btn-sm me-1"
                                                                onclick="rejectDocument({{ $doc->id }}, '{{ addslashes($doc->title) }}')"
                                                                title="Từ chối">
                                                                <i class="fas fa-times-circle"></i>
                                                            </button>
                                                        @endif

                                                        {{-- Chỉ hiển thị nút Sửa khi status = approved --}}
                                                        @if($doc->status === 'approved')
                                                            <a href="{{ route('admin.documentclub.edit', $doc->id) }}"
                                                                class="btn btn-primary btn-sm me-1" title="Sửa">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif

                                                        {{-- Tải xuống --}}
                                                        <a href="{{ route('admin.documentclub.download', $doc->id) }}"
                                                            class="btn btn-info btn-sm me-1" title="Tải xuống">
                                                            <i class="fas fa-download"></i>
                                                        </a>

                                                        {{-- Xóa --}}
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="deleteDocument({{ $doc->id }}, '{{ addslashes($doc->title) }}')"
                                                            title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted p-2">Không có tài liệu</p>
                    @endforelse
                </div>
            </div>
        @empty
            <p class="text-muted">Chưa có tài liệu nào.</p>
        @endforelse
    </div>

@endsection