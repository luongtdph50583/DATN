@extends('admin.layouts.app')

@section('title', 'Quản lý Tài liệu CLB')

@section('card-header')
    Tài liệu CLB
@endsection

@section('card-body')
        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif

        <div class="d-flex justify-content-between mb-3">
            <h4>Danh sách tài liệu theo CLB & Tag</h4>
            <a href="{{ route('admin.documentclub.create') }}" class="btn btn-primary">Thêm Tài liệu</a>
        </div>

        {{-- Bộ lọc & tìm kiếm --}}
        <div class="row g-2 mb-4">
            <div class="col-md-4">
                <input type="text" id="searchInput" class="form-control" placeholder="Tìm theo tên file, người tải, tiêu đề...">
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

        {{-- Container hiển thị tài liệu (cả mặc định và kết quả search) --}}
    <div id="documentResults">
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
                                <table class="table table-bordered mb-0">
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
                                                                                           <td>
    <!-- Xem -->
    <a href="{{ route('admin.documentclub.show', $doc->id) }}" class="btn btn-sm btn-outline-primary" title="Xem">
        <i class="fas fa-eye"></i>
    </a>

    @if($doc->status === 'pending')
        <!-- Duyệt -->
        <form action="{{ route('admin.documentclub.approve', $doc->id) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-sm btn-outline-success" title="Duyệt"><i class="fas fa-check"></i></button>
        </form>

        <!-- Từ chối -->
        <button class="btn btn-sm btn-outline-danger" title="Từ chối" onclick="rejectDocument({{ $doc->id }}, '{{ $doc->title }}')">
            <i class="fas fa-times"></i>
        </button>
    @elseif($doc->status === 'approved')
        <!-- Sửa -->
        <a href="{{ route('admin.documentclub.edit', $doc->id) }}" class="btn btn-sm btn-outline-warning" title="Sửa">
            <i class="fas fa-edit"></i>
        </a>
    @endif

    <!-- Tải xuống -->
    <a href="{{ route('admin.documentclub.download', $doc->id) }}" class="btn btn-sm btn-outline-success" title="Tải xuống">
        <i class="fas fa-download"></i>
    </a>

    <!-- Xóa -->
    <form action="{{ route('admin.documentclub.destroy', $doc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài liệu này?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-danger" title="Xóa">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</td>

                                                                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

        <style>
            table {
        table-layout: fixed;
        width: 90%;
    }

    table th, table td {
        word-wrap: break-word;
        text-align: center;
    }

        </style>
@endsection


   @push('scripts')
            <script>
                    function rejectDocument(docId, docTitle) {
                        let reason = prompt(`Nhập lý do từ chối tài liệu "${docTitle}":`);
                    if (reason !== null && reason.trim() !== '') {
                        // Tạo form động
                        let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/documentclub/${docId}/reject`; // route reject
                    form.style.display = 'none';

                    // CSRF token
                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}'; // Blade render
                    form.appendChild(csrf);

                    // Thêm method POST (nếu dùng PUT/PATCH thì thêm _method)
                    // let method = document.createElement('input');
                    // method.type = 'hidden';
                    // method.name = '_method';
                    // method.value = 'POST';
                    // form.appendChild(method);

                    // Thêm lý do
                    let reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'reason';
                    reasonInput.value = reason;
                    form.appendChild(reasonInput);

                    document.body.appendChild(form);
                    form.submit();
        }
    }


        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const typeFilter = document.getElementById('typeFilter');

            function fetchDocuments() {
                const search = searchInput.value.trim();
                const type = typeFilter.value;

                fetch(`{{ route('admin.documentclub.search') }}?search=${encodeURIComponent(search)}&type=${type}`)
                    .then(res => res.json())
                    .then(data => {
                        const container = document.getElementById('documentResults');
                        container.innerHTML = '';

                        // Lấy danh sách tất cả CLB từ data hoặc từ DOM cũ
                        const allClubs = new Set(data.map(doc => doc.club_name || 'Không CLB'));

                        // Nhóm dữ liệu theo CLB -> tag
                        const grouped = {};
                        data.forEach(doc => {
                            const club = doc.club_name || 'Không CLB';
                            const tags = doc.tags ? doc.tags.split(',').map(t => t.trim()) : ['Không có tag'];
                            if (!grouped[club]) grouped[club] = {};
                            tags.forEach(tag => {
                                if (!grouped[club][tag]) grouped[club][tag] = [];
                                grouped[club][tag].push(doc);
                            });
                        });

                        // Nếu CLB nào không có tài liệu, vẫn tạo empty object
                        allClubs.forEach(club => {
                            if (!grouped[club]) grouped[club] = { 'Không có tài liệu': [] };
                        });

                        // Render
                        for (const club in grouped) {
                            let htmlClub = `<div class="card mb-4"><div class="card-header bg-light"><strong>${club}</strong></div><div class="card-body p-0">`;

                            const clubTags = grouped[club];
                            const tagsList = Object.keys(clubTags);
                            if (tagsList.length === 0) {
                                htmlClub += `<p class="text-muted p-2">Không có tài liệu</p>`;
                            } else {
                                for (const tag in clubTags) {
                                    const docs = clubTags[tag];
                                    htmlClub += `<div class="mb-3"><div class="mb-1"><strong>Tag:</strong> ${tag} (${docs.length} tài liệu)</div>`;
                                    if (docs.length === 0) {
                                        htmlClub += `<p class="text-muted p-2">Không có tài liệu</p>`;
                                    } else {
                                        htmlClub += `<table class="table table-bordered mb-0"><thead><tr>

                                            <th>Tiêu đề</th><th>Người tải lên</th><th>Trạng thái</th><th>Loại</th><th>Hành động</th>
                                        </tr></thead><tbody>`;
                                        docs.forEach(doc => {
                                            htmlClub += `<tr>
                                                <td>${doc.title}</td>
                                                <td>${doc.uploader_name || '-'}</td>
                                                <td>${doc.status}</td>
                                                <td>${doc.file_type.toUpperCase()}</td>
                                                <td>
                                                    <a href="/admin/documentclub/${doc.id}" class="btn btn-sm btn-outline-primary">Xem</a>
                                                    ${doc.status === 'approved' ? `<a href="/admin/documentclub/${doc.id}/edit" class="btn btn-sm btn-outline-warning">Sửa</a>` : ''}
                                                    <a href="/admin/documentclub/${doc.id}/download" class="btn btn-sm btn-outline-success">Tải xuống</a>
                                                    <form action="/admin/documentclub/${doc.id}" method="POST" class="d-inline" onsubmit="return confirm('Xóa tài liệu này?')">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                                    </form>
                                                </td>
                                            </tr>`;
                                        });
                                        htmlClub += `</tbody></table>`;
                                    }
                                    htmlClub += `</div>`;
                                }
                            }

                            htmlClub += `</div></div>`;
                            container.innerHTML += htmlClub;
                        }
                    })
                    .catch(err => console.error(err));
            }

            // Gắn sự kiện
            searchInput.addEventListener('input', fetchDocuments);
            typeFilter.addEventListener('change', fetchDocuments);
        });
        </script>
@endpush
