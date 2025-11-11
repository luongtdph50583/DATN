@extends('admin.layouts.app')

@section('title', 'Thống kê Bài viết')

@section('card-body')
<div class="container-fluid">
   <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-4 text-gray-800">Thống kê Bài viết</h1>

        <a href="{{ route('admin.stats.index') }}" class="btn btn-secondary btn-sm">← Quay lại trang thống kê</a>
    </div>

    <!-- Thống kê nhanh -->
 <div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">Chờ duyệt: {{ $pendingCount }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">Đã duyệt: {{ $approvedCount }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">Bị từ chối: {{ $rejectedCount }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">Bài viết nổi bật: {{ $featuredCount }}</div>
        </div>
    </div>
</div>


    <!-- Bộ lọc -->
    <form method="GET" action="{{ route('admin.stats.posts') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <select name="status" class="form-control">
                <option value="">-- Tất cả trạng thái --</option>
                <option value="pending" {{ $status=='pending'?'selected':'' }}>Pending</option>
                <option value="approved" {{ $status=='approved'?'selected':'' }}>Approved</option>
                <option value="rejected" {{ $status=='rejected'?'selected':'' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="type" class="form-control">
                <option value="">-- Tất cả loại --</option>
                <option value="post" {{ $type=='post'?'selected':'' }}>Post</option>
                <option value="notice" {{ $type=='notice'?'selected':'' }}>Notice</option>
                <option value="document" {{ $type=='document'?'selected':'' }}>Document</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
        </div>
        <div class="col-md-2">
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
        </div>
    </form>

    <!-- Bảng danh sách bài viết -->
    <div class="card mb-4">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tiêu đề</th>
                        <th>CLB</th>
                        <th>Tác giả</th>
                        <th>Loại</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $i=>$post)
                        <tr>
                            <td>{{ $i+1+($posts->currentPage()-1)*$posts->perPage() }}</td>
                            <td>{{ $post->title }}</td>
                            <td>{{ $post->club->name ?? '-' }}</td>
                            <td>{{ $post->user->name ?? '-' }}</td>
                            <td>{{ ucfirst($post->type) }}</td>
                            <td>{{ ucfirst($post->status) }}</td>
                            <td>{{ $post->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <a href="#" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">Không có bài viết nào</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">{{ $posts->appends(request()->all())->links() }}</div>
        </div>
    </div>

    <!-- Biểu đồ bài viết theo tháng -->
    <div class="card mb-4">
        <div class="card-body">
            <canvas id="postsChart" height="90"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxPosts = document.getElementById('postsChart').getContext('2d');
const postsChart = new Chart(ctxPosts, {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Số lượng bài viết mới',
            data: @json($postsPerMonth),
            fill: false,
            borderColor: 'rgba(54, 162, 235, 1)',
            tension: 0.3,
            borderWidth: 2
        }]
    },
    options: { responsive: true }
});
</script>
@endpush
