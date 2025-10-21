@extends('admin.layouts.app')

@section('title')
    Quản lý tài liệu
@endsection

@section('card-title')
    Quản lý tài liệu
@endsection

@section('card-header')
    Danh sách tài liệu
@endsection

@section('card-body')
    <div class="mb-4">
        <div class="row g-3 align-items-end">
            <!-- Ô tìm kiếm -->
            <div class="col-md-4">
                <label for="filterKeyword" class="form-label">Tìm kiếm (tên file hoặc người upload)</label>
                <input type="text" id="filterKeyword" class="form-control" placeholder="Nhập từ khóa...">
            </div>

            <!-- Lọc theo định dạng -->
            <div class="col-md-3">
                <label for="filterType" class="form-label">Định dạng</label>
                <select id="filterType" class="form-select">
                    <option value="">Tất cả định dạng</option>
                    @foreach($fileTypes as $type)
                        <option value="{{ $type }}">{{ strtoupper($type) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Bảng hiển thị -->
    <div class="table-responsive">
        <table class="table table-bordered" id="documentsTable" width="100%" cellspacing="0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên file</th>
                    <th>Loại</th>
                    <th>Người upload</th>
                    <th>Ngày tải lên</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="mediaTableBody">
                @forelse($media as $item)
                    @php
                        $fileUrl = asset('storage/' . $item->file_path);
                        $mime = strtolower($item->file_type);
                        $canPreview = str_starts_with($mime, 'image/') || str_contains($mime, 'pdf') || str_starts_with($mime, 'video/');
                    @endphp
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->file_name }}</td>
                        <td>{{ $item->file_type }}</td>
                        <td>{{ $item->uploader->name ?? 'N/A' }}</td>
                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>
                            @if($canPreview)
                                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">Xem</a>
                            @endif
                            <a href="{{ $fileUrl }}" download class="btn btn-sm btn-success">Tải xuống</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Chưa có tài liệu nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        const keywordInput = document.getElementById('filterKeyword');
        const typeSelect = document.getElementById('filterType');
        const tbody = document.getElementById('mediaTableBody');
        let timer = null;

        function fetchFilteredData() {
            const keyword = keywordInput.value.trim();
            const fileType = typeSelect.value;

            fetch('{{ route('admin.documents.filter') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    keyword: keyword,
                    file_type: fileType
                })
            })
            .then(res => res.json())
            .then(res => {
                tbody.innerHTML = '';
                if (!res.data || res.data.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-muted">Không có tài liệu phù hợp.</td>
                        </tr>`;
                    return;
                }

                res.data.forEach(item => {
                    const uploaderName = item.uploader?.name ?? 'N/A';
                    const mime = item.file_type.toLowerCase();
                    const canPreview = mime.startsWith('image/') || mime.includes('pdf') || mime.startsWith('video/');
                    const fileUrl = `/storage/${item.file_path}`;
                    const viewBtn = canPreview ? `<a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-primary">Xem</a>` : '';

                    tbody.innerHTML += `
                        <tr>
                            <td>${item.id}</td>
                            <td>${item.file_name}</td>
                            <td>${item.file_type}</td>
                            <td>${uploaderName}</td>
                            <td>${new Date(item.created_at).toLocaleDateString('vi-VN')}</td>
                            <td>
                                ${viewBtn}
                                <a href="${fileUrl}" download class="btn btn-sm btn-success">Tải xuống</a>
                            </td>
                        </tr>`;
                });
            })
            .catch(err => console.error('Lỗi khi lọc:', err));
        }

        keywordInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(fetchFilteredData, 400);
        });

        typeSelect.addEventListener('change', fetchFilteredData);
    </script>
@endsection
