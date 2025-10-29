<table class="table table-bordered table-striped">
    <thead class="table-secondary">
        <tr>
            <th>Tên CLB</th>
            <th>Số thành viên</th>
            <th>Số sự kiện</th>
            <th>Trạng thái</th>
            <th>Ngày thành lập</th>
             <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($clubs as $club)
            <tr>
                <td>{{ $club->name }}</td>
                <td>{{ $club->members_count }}</td>
                <td>{{ $club->events_count }}</td>
                <td>
                    @if ($club->status === 'active')
                        <span class="badge bg-success">Đang hoạt động</span>
                    @elseif ($club->status === 'pending')
                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                    @else
                        <span class="badge bg-danger">Ngưng hoạt động</span>
                    @endif
                </td>
                <td>{{ $club->created_at->format('d/m/Y') }}</td>
                <td>
                                    <a href="{{ route('admin.clubs.report.show', $club->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        Xem báo cáo
                                    </a>
                                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center text-muted">Không tìm thấy câu lạc bộ nào</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-3">
    {{ $clubs->links() }}
</div>
