@extends('admin.layouts.app')
@section('title', 'Thùng rác - Thành viên')

@section('card-body')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Thùng rác Thành viên</h1>
            <p class="text-muted small">Các thành viên đã bị xóa mềm</p>
        </div>
        <a href="{{ route('admin.members.index') }}" class="btn btn-primary">
            Quay lại danh sách
        </a>
    </div>

    @if($members->count() > 0)
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-danger">
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>MSSV</th>
                        <th>Email</th>
                        <th>Xóa lúc</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($members as $member)
                    <tr>
                        <td>#{{ $member->id }}</td>
                        <td class="fw-bold">{{ $member->user->name }}</td>
                        <td><code>{{ $member->student_code }}</code></td>
                        <td>{{ $member->user->email }}</td>
                        <td>{{ $member->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <!-- KHÔI PHỤC -->
                            <form action="{{ route('admin.members.restore', $member->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Khôi phục</button>
                            </form>

                            <!-- XÓA VĨNH VIỄN -->
                            <form action="{{ route('admin.members.forceDelete', $member->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('XÓA VĨNH VIỄN? Không thể khôi phục!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Xóa vĩnh viễn</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $members->links() }}
        </div>
    </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-trash-restore fa-3x text-success mb-3"></i>
            <h5>Thùng rác trống!</h5>
        </div>
    @endif
</div>
@endsection