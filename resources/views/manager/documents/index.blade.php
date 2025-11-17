@extends('manager.layouts.app')

@section('title', 'Quản lý tài liệu CLB')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Tài liệu CLB</h3>
        <a href="{{ route('manager.document.create') }}" class="btn btn-primary">Thêm tài liệu mới</a>
    </div>

    <!-- Filter và Search -->
    <div class="row mb-3">
        <div class="col-md-4">
            <select id="typeFilter" class="form-select">
                @foreach($typeOptions as $key => $label)
                    <option value="{{ $key }}" {{ $selectedType == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm tài liệu..." value="{{ $search }}">
        </div>
        <div class="col-md-4">
            <button id="searchBtn" class="btn btn-secondary">Tìm kiếm</button>
        </div>
    </div>

    @foreach($documentsByTag as $tag => $docs)
    <div class="card mb-3">
        <div class="card-header bg-light">
            <strong>Tag: {{ is_array($tag) ? implode(', ', $tag) : $tag }}</strong>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tiêu đề</th>
                        <th>File</th>
                        <th>Loại</th>
                        <th>Ngày tải lên</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($docs as $doc)
                    <tr>
                        <td>{{ $doc->title }}</td>
                        <td>{{ $doc->file_name }}</td>
                        <td>{{ $doc->file_type }}</td>
                        <td>{{ $doc->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('manager.document.download', $doc->id) }}" class="btn btn-sm btn-success">Download</a>
                            <a href="{{ route('manager.document.edit', $doc->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                            <form action="{{ route('manager.document.destroy', $doc->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa tài liệu này?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>

<script>
document.getElementById('searchBtn').addEventListener('click', function(){
    let keyword = document.getElementById('searchInput').value;
    let type = document.getElementById('typeFilter').value;
    window.location.href = "{{ route('manager.document.index') }}" + "?search=" + keyword + "&type=" + type;
});
</script>
@endsection
