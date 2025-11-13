@extends('admin.layouts.app')

@section('title', 'Quản lý Tài liệu CLB')

@section('card-header')
    Tài liệu CLB
@endsection

@push('scripts')
    <script>
        function rejectDocument(docId, docTitle) {
            const reason = prompt(`Nhập lý do từ chối tài liệu "${docTitle}":`);

            if (reason === null || reason.trim() === '') {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/documentclub') }}/${docId}/reject`;
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
    </script>
@endpush

@section('card-body')
    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
        <h4 class="mb-0">Danh sách tài liệu theo CLB & Tag</h4>
        <a href="{{ route('admin.documentclub.create') }}" class="btn btn-outline-primary">
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
            <button type="submit" class="btn btn-outline-primary">
                <i class="bi bi-search me-1"></i> Lọc
            </button>
        </div>
        <div class="col-md-3 col-lg-2 d-grid">
            <a href="{{ route('admin.documentclub.index') }}" class="btn btn-outline-secondary">
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
    <div class="d-flex flex-wrap gap-2">
        <!-- Xem -->
        <a href="{{ route('admin.documentclub.show', $doc->id) }}"
            class="btn btn-sm btn-primary" title="Xem">
            <i class="bi bi-eye">xem</i>
        </a>

        @if($doc->status === 'pending')
            <!-- Duyệt -->
            <form action="{{ route('admin.documentclub.approve', $doc->id) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-success" title="Duyệt">
                    <i class="bi bi-check-circle">duyệt</i>
                </button>
            </form>

            <!-- Từ chối -->
            <button class="btn btn-sm btn-danger" title="Từ chối"
                onclick="rejectDocument({{ $doc->id }}, @json($doc->title))">
                <i class="bi bi-x-circle">từ chối</i>
            </button>
        @elseif($doc->status === 'approved')
            <!-- Sửa -->
            <a href="{{ route('admin.documentclub.edit', $doc->id) }}"
                class="btn btn-sm btn-warning" title="Sửa">
                <i class="bi bi-pencil-square">sửa</i>
            </a>
        @endif

        <!-- Tải xuống -->
        <a href="{{ route('admin.documentclub.download', $doc->id) }}"
            class="btn btn-sm btn-success" title="Tải xuống">
            <i class="bi bi-download">tải xuống</i>
        </a>

        <!-- Xóa -->
        <form action="{{ route('admin.documentclub.destroy', $doc->id) }}" method="POST"
            class="d-inline" onsubmit="return confirm('Xóa tài liệu này?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger" title="Xóa">
                <i class="bi bi-trash">xóa</i>
            </button>
        </form>
    </div>
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
