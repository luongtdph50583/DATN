@include('admin.layouts.header')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê</h1>
    </div>

    <!-- Content Row - Tổng quan -->
    <div class="row">
        <!-- Tổng số CLB -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tổng số CLB
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $clubCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.admin.stats.clubs') }}" class="small stretched-link text-warning">Xem</a>
                </div>
            </div>
        </div>

        <!-- Tổng số Thành viên -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tổng số Thành viên
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $memberCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.admin.stats.members') }}" class="small stretched-link text-primary">Xem</a>
                </div>
            </div>
        </div>

        <!-- Tổng số Sự kiện -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tổng số Sự kiện
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $eventCount }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.admin.stats.events') }}" class="small stretched-link text-info">Xem</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Biểu đồ -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Biểu đồ số lượng CLB, Thành viên, Sự kiện theo tháng</h6>
                </div>
                <div class="card-body">
                    <canvas id="statsChart"></canvas>
                    <script>
                        const ctx = document.getElementById('statsChart').getContext('2d');
                        const statsChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                                         'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                                datasets: [
                                    {
                                        label: 'CLB',
                                        data: @json($clubsPerMonth),
                                        backgroundColor: 'rgba(255, 206, 86, 0.5)',
                                        borderColor: 'rgba(255, 206, 86, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Thành viên',
                                        data: @json($membersPerMonth),
                                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1
                                    },
                                    {
                                        label: 'Sự kiện',
                                        data: @json($eventsPerMonth),
                                        backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                        borderColor: 'rgba(75, 192, 192, 1)',
                                        borderWidth: 1
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
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
    </div>

</div>

@include('admin.layouts.footer')
