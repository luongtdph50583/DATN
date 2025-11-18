@extends('admin.layouts.app')

@section('title', 'Thống kê tổng quan')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
@endpush

@section('card-body')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Thống kê</h1>
    </div>

    <!-- Content Row - Tổng quan (6 ô) -->
    <div class="row">
        <!-- Tổng số CLB -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tổng số CLB</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $clubCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.stats.clubs') }}" class="small stretched-link text-warning">Xem</a>
                </div>
            </div>
        </div>

        <!-- Tổng số Thành viên -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng số Thành viên</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $memberCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.stats.members') }}" class="small stretched-link text-primary">Xem</a>
                </div>
            </div>
        </div>

        <!-- Tổng số Sự kiện -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tổng số Sự kiện</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $eventCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.stats.events') }}" class="small stretched-link text-info">Xem</a>
                </div>
            </div>
        </div>

        <!-- Tổng số Quỹ -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Số Lượng giao dịch </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $fundCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.stats.funds') }}" class="small stretched-link text-success">Xem</a>
                </div>
            </div>
        </div>

        <!-- Tổng số Bài viết -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Tổng số Bài viết</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $postCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.stats.posts') }}"" class="small stretched-link text-secondary">Xem</a>
                </div>
            </div>
        </div>

        <!-- Tổng số Tài khoản -->
        <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Tổng số Tài khoản</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $accountCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.stats.accounts') }}" class="small stretched-link text-dark">Xem</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc thời gian (from / to) -->
    <form method="GET" action="{{ route('admin.stats.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="start_date" class="form-label">Từ ngày</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">Đến ngày</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Lọc</button>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" name="reset" value="true" class="btn btn-secondary w-100">Đặt lại</button>
        </div>
    </form>

    <!-- Biểu đồ: số lượng CLB, Thành viên, Sự kiện, Quỹ, Bài viết, Tài khoản theo tháng -->
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Biểu đồ số lượng CLB, Thành viên, Sự kiện, Quỹ, Bài viết, Tài khoản theo tháng</h6>
                </div>
                <div class="card-body">
                    <canvas id="statsChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Nếu project chưa load Chart.js toàn cục, bạn có thể bỏ comment dòng CDN dưới đây -->
<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('statsChart').getContext('2d');

    const statsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels ?? []),
            datasets: [
                {
                    label: 'CLB',
                    data: @json($clubsPerMonth ?? []),
                    borderColor: 'rgba(255, 206, 86, 1)',
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Thành viên',
                    data: @json($membersPerMonth ?? []),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Sự kiện',
                    data: @json($eventsPerMonth ?? []),
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Quỹ',
                    data: @json($fundsPerMonth ?? []),
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Bài viết',
                    data: @json($postsPerMonth ?? []),
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Tài khoản',
                    data: @json($accountsPerMonth ?? []),
                    borderColor: 'rgba(100, 100, 100, 1)',
                    backgroundColor: 'rgba(100, 100, 100, 0.15)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            stacked: false,
            scales: {
                y: { beginAtZero: true }
            },
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false }
            }
        }
    });
});
</script>
@endpush
