@extends('admin.layouts.app')

@section('card-title', 'Thùng rác CLB')

@section('card-body')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>🗑️ Thùng rác CLB</h2>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-secondary">⬅️ Quay lại danh sách</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($clubs->count())
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tên CLB</th>
                    <th>Ngày xóa</th>
                    <th>Lý do</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clubs as $club)
                    <tr>
                        <td>{{ $club->name }}</td>
                        <td>{{ $club->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $club->deleted_reason ?? 'Không có' }}</td>
                        <td>
                            <form action="{{ route('admin.clubs.restore', $club->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-success">♻️ Khôi phục</button>
                            </form>

                            <form action="{{ route('admin.clubs.forceDelete', $club->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa vĩnh viễn CLB này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">❌ Xóa vĩnh viễn</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $clubs->links() }}
    @else
        <p class="text-muted">Không có CLB nào trong thùng rác.</p>
    @endif
</div>
@endsection
