
@include('admin.layouts.header')


<div class="container-fluid">
    <!-- Page Heading -->
       <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê CLB</h1>
        <!-- ✅ Nút quay lại -->
        <a href="{{ route('admin.admin.stats') }}" class="btn btn-secondary btn-sm">
            ← Quay lại trang thống kê
        </a>
    </div>

    <!-- Dropdown chọn phương thức sắp xếp -->
    <div class="row mb-4">
        <div class="col-md-4">
            <select id="sortClubs" class="form-control">
                <option value="top_members" {{ $sort=='top_members'?'selected':'' }}>Top CLB đông thành viên nhất</option>
                <option value="least_members" {{ $sort=='least_members'?'selected':'' }}>Top CLB ít thành viên nhất</option>
                <option value="oldest" {{ $sort=='oldest'?'selected':'' }}>Top CLB thành lập sớm nhất</option>
                <option value="most_events" {{ $sort=='most_events'?'selected':'' }}>Top CLB nhiều sự kiện nhất</option>
            </select>
        </div>
    </div>

    <!-- Bảng thống kê CLB -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách CLB</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên CLB</th>
                                    <th>Thành viên</th>
                                    <th>Ngày thành lập</th>
                                    <th>Số sự kiện</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clubs as $i => $club)
                                <tr>
                                    <td>{{ $club->id}}</td>
                                    <td>{{ $club->name }}</td>
                                    <td>{{ $club->members_count }}</td>
                                    <td>{{ $club->created_at->format('Y-m-d') }}</td>
                                    <td>{{ $club->events_count }}</td>
                                    <td>
                                        <a href="{{ route('admin.clubs.report', $club->id) }}" class="btn btn-sm btn-primary">
    Xem báo cáo
</a>

                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- /.table-responsive -->
                     <div class="d-flex justify-content-center mt-3">
                        {{ $clubs->appends(['sort' => $sort])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>

@include('admin.layouts.footer')

<script>
document.getElementById('sortClubs').addEventListener('change', function() {
    const sort = this.value;
    window.location.href = '{{ route("admin.admin.stats.clubs") }}?sort=' + sort;
});
</script>
