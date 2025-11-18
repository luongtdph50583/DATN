@extends('client.layouts.app')
@section('title', $club->name)

@section('content')
    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h2 class="mb-0">{{ $club->name }}</h2>
                        @if($club->slogan)
                            <p class="text-muted mb-0"><em>{{ $club->slogan }}</em></p>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($club->logo)
                            <div class="text-center mb-3">
                                <img src="{{ asset('storage/' . $club->logo) }}" alt="{{ $club->name }}" class="img-fluid" style="max-height: 200px;">
                            </div>
                        @endif

                        <div class="mb-3">
                            <h5>Giới thiệu</h5>
                            <p>{{ $club->description ?? 'Chưa có mô tả.' }}</p>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Lĩnh vực:</strong> {{ $club->field }}
                            </div>
                            <div class="col-md-6">
                                <strong>Trạng thái:</strong> 
                                <span class="badge bg-{{ $club->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ $club->status === 'active' ? 'Hoạt động' : 'Ngưng hoạt động' }}
                                </span>
                            </div>
                        </div>

                        @if($club->email || $club->phone)
                            <div class="row mb-3">
                                @if($club->email)
                                    <div class="col-md-6">
                                        <strong>Email:</strong> <a href="mailto:{{ $club->email }}">{{ $club->email }}</a>
                                    </div>
                                @endif
                                @if($club->phone)
                                    <div class="col-md-6">
                                        <strong>Điện thoại:</strong> {{ $club->phone }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($club->location)
                            <div class="mb-3">
                                <strong>Địa điểm:</strong> {{ $club->location }}
                            </div>
                        @endif

                        @if($club->rules)
                            <div class="mb-3">
                                <h5>Nội quy</h5>
                                <p>{{ $club->rules }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Bài viết mới nhất</h5>
                    </div>
                    <div class="card-body">
                        @forelse($club->posts as $post)
                            <div class="mb-3 pb-3 border-bottom">
                                <h6><a href="{{ route('client.home') }}">{{ $post->title }}</a></h6>
                                <p class="text-muted small mb-2">
                                    {{ $post->created_at->format('d/m/Y H:i') }} - 
                                    {{ $post->user->name ?? 'Chưa có tác giả' }}
                                </p>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($post->content), 150) }}</p>
                            </div>
                        @empty
                            <p class="text-muted">Chưa có bài viết nào.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin CLB</h5>
                    </div>
                    <div class="card-body">
                        @if($club->founded_at)
                            <p><strong>Thành lập:</strong> {{ \Carbon\Carbon::parse($club->founded_at)->format('d/m/Y') }}</p>
                        @endif

                        @if($club->member_limit)
                            <p><strong>Giới hạn thành viên:</strong> {{ $club->member_limit }}</p>
                        @endif

                        <p><strong>Số thành viên:</strong> {{ $club->clubMembers->count() }}</p>

                        @if($club->manager)
                            <p><strong>Chủ nhiệm:</strong> {{ $club->manager->name }}</p>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Sự kiện sắp tới</h5>
                    </div>
                    <div class="card-body">
                        @forelse($club->events as $event)
                            <div class="mb-3 pb-3 border-bottom">
                                <h6>{{ $event->name }}</h6>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y H:i') }}
                                </p>
                                @if($event->location)
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                                    </p>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted">Chưa có sự kiện nào.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

