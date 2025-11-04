@extends('admin.layouts.app')

@section('title', 'Quản lý Tài liệu CLB')

@section('card-header')
    Tài liệu CLB
@endsection

@section('card-body')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between mb-3">
        <h4>Danh sách tài liệu theo Tag</h4>
        <a href="{{ route('admin.documentclub.create') }}" class="btn btn-primary">Thêm Tài liệu</a>
    </div>

    {{-- Bộ lọc và tìm kiếm realtime --}}
    <div class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Tìm theo tên file , người tải , tiêu đề">
        </div>
        <div class="col-md-3">
            <select id="typeFilter" class="form-select">
                <option value="all">Tất cả loại</option>
                <option value="pdf">PDF</option>
                <option value="doc">Word</option>
                <option value="xls">Excel</option>
                <option value="jpg">Hình ảnh</option>
                <option value="mp3">Âm thanh</option>
                <option value="mp4">Video</option>
            </select>
        </div>
    </div>

    {{-- Kết quả realtime --}}
    <div id="documentResults">
        @forelse($documentsByTag as $tag => $docs)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <span class="badge bg-secondary">{{ $tag ?: 'Không có tag' }}</span>
                        ({{ $docs->count() }} tài liệu)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tiêu đề</th>
                                <th>CLB</th>
                                <th>Người tải lên</th>
                                <th>Mức truy cập</th>
                                <th>Dạng tài liệu</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($docs as $doc)
                                <tr>
                                    <td>{{ $doc->title }}</td>
                                    <td>{{ $doc->club->name ?? '-' }}</td>
                                    <td>{{ $doc->uploader->name ?? '-' }}</td>
                                    <td>
                                        @php
                                        $levelColor = match ($doc->access_level) {
                                            'public' => 'success',
                                            'member' => 'primary',
                                            'club_manager' => 'info',
                                            'admin' => 'warning',
                                            default => 'secondary'
                                        };
                                        @endphp
                                        <span class="badge bg-{{ $levelColor }}">{{ strtoupper($doc->access_level) }}</span>
                                    </td>
                                    <td>
                                        @php
                                        $type = strtolower($doc->file_type);
                                        $typeLabel = match (true) {
                                            in_array($type, ['pdf']) => 'PDF',
                                            in_array($type, ['doc', 'docx']) => 'Word',
                                            in_array($type, ['xls', 'xlsx']) => 'Excel',
                                            in_array($type, ['jpg', 'jpeg', 'png', 'svg']) => 'Hình ảnh',
                                            in_array($type, ['mp3', 'wav']) => 'Âm thanh',
                                            in_array($type, ['mp4']) => 'Video',
                                            default => strtoupper($type)
                                        };
                                        @endphp
                                        <span class="badge bg-dark">{{ $typeLabel }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="{{ route('admin.documentclub.download', $doc->id) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="bi bi-download"></i> Tải xuống
                                            </a>
                                            <a href="{{ route('admin.documentclub.edit', $doc->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil-square"></i> Sửa
                                            </a>
                                            <a href="{{ route('admin.documentclub.show', $doc->id) }}" class="btn btn-info btn-sm">
                                                <i class="bi bi-eye"></i> Xem chi tiết
                                            </a>
                                            <form action="{{ route('admin.documentclub.destroy', $doc->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa tài liệu này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <p class="text-muted">Chưa có tài liệu nào.</p>
        @endforelse
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const typeFilter = document.getElementById('typeFilter');
            const resultsContainer = document.getElementById('documentResults');

            function fetchDocuments() {
                const search = searchInput.value;
                const type = typeFilter.value;

                fetch(`{{ route('admin.documentclub.search') }}?search=${encodeURIComponent(search)}&type=${type}`)
                    .then(res => res.json())
                    .then(data => {
                        resultsContainer.innerHTML = '';

                        if (data.length === 0) {
                            resultsContainer.innerHTML = '<p class="text-muted">Không tìm thấy tài liệu nào.</p>';
                            return;
                        }

                        const grouped = {};
                        data.forEach(doc => {
                            const tag = doc.tags || 'Không có tag';
                            if (!grouped[tag]) grouped[tag] = [];
                            grouped[tag].push(doc);
                        });

                        for (const tag in grouped) {
                            const docs = grouped[tag];
                            let html = `
                                    <div class="card mb-4 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">
                                                <span class="badge bg-secondary">${tag}</span>
                                                (${docs.length} tài liệu)
                                            </h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-striped mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Tiêu đề</th>
                                                        <th>CLB</th>
                                                        <th>Người tải lên</th>
                                                        <th>Mức truy cập</th>
                                                        <th>Dạng tài liệu</th>
                                                        <th>Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                `;

                            docs.forEach(doc => {
                                const levelColor = {
                                    public: 'success',
                                    member: 'primary',
                                    club_manager: 'info',
                                    admin: 'warning'
                                }[doc.access_level] || 'secondary';

                                const type = doc.file_type.toLowerCase();
                                const typeLabel = ['pdf'].includes(type) ? 'PDF'
                                    : ['doc', 'docx'].includes(type) ? 'Word'
                                        : ['xls', 'xlsx'].includes(type) ? 'Excel'
                                            : ['jpg', 'jpeg', 'png', 'svg'].includes(type) ? 'Hình ảnh'
                                                : ['mp3', 'wav'].includes(type) ? 'Âm thanh'
                                                    : ['mp4'].includes(type) ? 'Video'
                                                        : type.toUpperCase();

                                html += `
                                        <tr>
                                            <td>${doc.title}</td>
                                            <td>${doc.club?.name ?? '-'}</td>
                                            <td>${doc.uploader?.name ?? '-'}</td>
                                            <td><span class="badge bg-${levelColor}">${doc.access_level.toUpperCase()}</span></td>
                                            <td><span class="badge bg-dark">${typeLabel}</span></td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="/admin/documentclub/${doc.id}/download" class="btn btn-success btn-sm">
                                                        <i class="bi bi-download"></i> Tải xuống
                                                    </a>
                                                    <a href="/admin/documentclub/${doc.id}/edit" class="btn btn-warning btn-sm">
                                                        <i class="bi bi-pencil-square"></i> Sửa
                                                    </a>
                                                    <a href="/admin/documentclub                                                <a href="/admin/documentclub/${doc.id}" class="btn btn-info btn-sm">
                                                        <i class="bi bi-eye"></i> Xem chi tiết
                                                    </a>
                                                    <form action="/admin/documentclub/${doc.id}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Bạn có chắc muốn xóa tài liệu này?')">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button class="btn btn-danger btn-sm">
                                                            <i class="bi bi-trash"></i> Xóa
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                            });

                            html += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                `;

                            resultsContainer.innerHTML += html;
                        }
                    });
            }

            searchInput.addEventListener('input', fetchDocuments);
            typeFilter.addEventListener('change', fetchDocuments);
            fetchDocuments(); // gọi lần đầu
        });
                                                    </script>
@endsection

