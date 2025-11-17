<div class="container mt-3">
    <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-start">
            <i class="fas fa-bell me-2 mt-1"></i>
            <div>
                <strong>Bạn có {{ $notifications->count() }} thông báo mới.</strong>
                <ul class="mb-0 ps-3 small">
                    @foreach($notifications as $notification)
                        <li>
                            <div class="fw-semibold">
                                {{ data_get($notification->data, 'title', 'Thông báo') }}
                                <span class="text-muted">
                                    ({{ optional($notification->created_at)->diffForHumans() }})
                                </span>
                            </div>
                            @php($messageHtml = data_get($notification->data, 'message_html'))
                            <div class="text-muted small">
                                @if($messageHtml)
                                    {!! $messageHtml !!}
                                @else
                                    {{ data_get($notification->data, 'message', '') }}
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <form action="{{ route('notifications.read') }}" method="POST" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-primary">
                Đánh dấu đã đọc
            </button>
        </form>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>

