@include('admin.layouts.header')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    </div>

    <!-- Content Row - Tổng quan -->
    <div class="row">
        <!-- Tổng số Thành viên -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số Thành viên
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $memberCount = \App\Models\User::where('role', 'member')->count();
                                    echo $memberCount;
                                @endphp
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.members.index') }}" class="small stretched-link text-primary">Quản lý Thành viên</a>
                </div>
            </div>
        </div>

        <!-- Tổng số Quản lý CLB -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Tổng số Quản lý CLB
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $managerCount = \App\Models\User::where('role', 'club_manager')->count();
                                    echo $managerCount;
                                @endphp
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="#" class="small stretched-link text-success">Quản lý Quản lý CLB</a> <!-- Chưa có route, cần thêm -->
                </div>
                
            </div>
        </div>


        <!-- Tổng số Sự kiện -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tổng số Sự kiện
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $eventCount = \App\Models\Event::count();
                                    echo $eventCount;
                                @endphp
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.events.index') }}" class="small stretched-link text-info">Quản lý Sự kiện</a>
                </div>
            </div>
        </div>

        <!-- Tổng số CLB -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tổng số CLB
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $clubCount = \App\Models\Club::count();
                                    echo $clubCount;
                                @endphp
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="#" class="small stretched-link text-warning">Quản lý CLB</a> <!-- Chưa có route, cần thêm -->
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Biểu đồ -->
    <div class="row">
        <!-- Area Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sự kiện theo Trạng thái</h6>
                </div>
                <div class="card-body">
                    <canvas id="eventStatusChart"></canvas>
                    <script>
                        const ctx = document.getElementById('eventStatusChart').getContext('2d');
                        const eventStatusChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Chờ duyệt', 'Đã duyệt', 'Bị từ chối'],
                                datasets: [{
                                    label: 'Số lượng',
                                    data: [
                                        @php echo \App\Models\Event::where('status', 'pending')->count(); @endphp,
                                        @php echo \App\Models\Event::where('status', 'approved')->count(); @endphp,
                                        @php echo \App\Models\Event::where('status', 'rejected')->count(); @endphp
                                    ],
                                    backgroundColor: ['rgba(54, 162, 235, 0.5)', 'rgba(75, 192, 192, 0.5)', 'rgba(255, 99, 132, 0.5)'],
                                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    </script>
                </div>
            </div>
        </div>

        <!-- Thông tin bổ sung -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông báo</h6>
                </div>
                <div class="card-body">
                    <p>Chào mừng đến với trang quản trị LBTH!</p>
                    <p>- Kiểm tra danh sách thành viên mới.</p>
                    <p>- Duyệt các sự kiện sắp diễn ra.</p>
                    <p><a href="#" class="small text-info">Xem tất cả thông báo</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.layouts.footer')