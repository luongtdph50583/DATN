@include('admin.layouts.header')

<!-- Nội dung trang -->
@if (auth()->check())
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="notificationDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Thông báo <span class="badge badge-pill badge-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
        </button>
        <div class="dropdown-menu" aria-labelledby="notificationDropdown">
            @forelse (auth()->user()->unreadNotifications as $notification)
                <a class="dropdown-item" href="{{ $notification->data['url'] }}">
                    <strong>{{ $notification->data['title'] }}</strong><br>
                    {{ Str::limit($notification->data['message'], 50) }}<br>
                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                </a>
                <div class="dropdown-divider"></div>
            @empty
                <a class="dropdown-item" href="#">Không có thông báo mới</a>
            @endforelse
        </div>
    </div>
@endif
<div class="container-fluid">
    @yield('content')
</div>

@include('admin.layouts.footer')
