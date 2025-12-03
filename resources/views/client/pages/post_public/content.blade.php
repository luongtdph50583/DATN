<section class="news-standard-section section-padding">
    <div class="container">
        <div class="gt-news-standard-wrapper">
            <div class="row g-4">
                <!-- Danh sách bài viết -->
                <div class="col-12 col-lg-8">
                    <div class="gt-news-standard-items">
                        @forelse ($posts as $post)
                            <div class="gt-news-card-items-4">

                                {{-- Ảnh ratio 16:9 --}}
                                <div class="gt-news-image"
                                    style="width: 100%; aspect-ratio: 16 / 9; overflow: hidden; border-radius: 8px;">
                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="img"
                                        style="width: 100%; height: 100%; object-fit: cover; object-position: center; display: block;">
                                </div>

                            <div class="gt-news-content">
                            <ul class="gt-date-list">
                                <li>
                                    <i class="fa-solid fa-calendar-days"></i>
                                    {{ $post->created_at->format('d M Y') }}
                                </li>
                                <li>
                                    <i class="fa-solid fa-comments"></i>
                                    19 Comments
                                </li>
                                <li>
                                    <i class="fa-solid fa-eye"></i>
                                    {{ $post->views }} lượt xem
                                </li>
                            </ul>


                                <h3>
                                    <a href="{{ route('client.postpublic.show', $post->id) }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                <p>
                                    <a href="{{ route('client.postpublic.show', $post->id) }}" style="color: inherit; text-decoration: none;">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 70) }}
                                    </a>
                                </p>
                            </div>


                            </div>
                            <hr>
                        @empty
                            <p>Không có bài viết nào</p>
                        @endforelse

                        <!-- Phân trang giữ nguyên layout -->
                        <div class="page-nav-wrap text-center">
                            <ul>
                                {{-- Nút Previous --}}
                                @if ($posts->onFirstPage())
                                    <li><span class="page-numbers disabled"><i class="fa-solid fa-arrow-up-left"></i></span>
                                    </li>
                                @else
                                    <li><a class="page-numbers" href="{{ $posts->previousPageUrl() }}"><i
                                                class="fa-solid fa-arrow-up-left"></i></a></li>
                                @endif

                                {{-- Danh sách phân trang --}}
                                @foreach ($posts->links()->elements[0] as $page => $url)
                                    @if ($page == $posts->currentPage())
                                        <li class="active"><span class="page-numbers">{{ sprintf('%02d', $page) }}</span></li>
                                    @else
                                        <li><a class="page-numbers" href="{{ $url }}">{{ sprintf('%02d', $page) }}</a></li>
                                    @endif
                                @endforeach

                                {{-- Nút Next --}}
                                @if ($posts->hasMorePages())
                                    <li><a class="page-numbers" href="{{ $posts->nextPageUrl() }}"><i
                                                class="fa-solid fa-arrow-up-right"></i></a></li>
                                @else
                                    <li><span class="page-numbers disabled"><i
                                                class="fa-solid fa-arrow-up-right"></i></span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
            <div class="col-lg-4 col-12">
                <div class="gt-main-sideber sticky-style">
            
                    <!-- Recent Posts -->
                    <div class="gt-single-sideber-widget">
                        <div class="gt-widget-title">
                            <h3>Recent Post</h3>
                        </div>
                        <div class="gt-recent-post-area">
                            @forelse ($recentPosts as $rp)
                                <div class="gt-recent-items d-flex align-items-center gap-3">
                                    <div class="gt-recent-thumb">
                                        <a href="{{ route('client.postpublic.show', $rp->id) }}">
                                            <img src="{{ asset('storage/' . $rp->thumbnail) }}" alt="img"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        </a>
                                    </div>
                                    <div class="gt-recent-content">
                                        <h5>
                                            <a href="{{ route('client.postpublic.show', $rp->id) }}" class="text-decoration-none">
                                                {{ $rp->title }}
                                            </a>
                                        </h5>
                                        <ul>
                                            <li>{{ $rp->created_at->format('M d, Y') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            @empty
                                <p>Không có bài viết nào</p>
                            @endforelse
                        </div>
                    </div>
            
                    <!-- Featured Posts -->
                    <div class="gt-single-sideber-widget">
                        <div class="gt-widget-title">
                            <h3>Bài viết nổi bật</h3>
                        </div>
                        <div class="gt-recent-post-area">
                            @forelse ($featuredPosts as $fp)
                                <div class="gt-recent-items d-flex align-items-center gap-3">
                                    <div class="gt-recent-thumb">
                                        <a href="{{ route('client.postpublic.show', $fp->id) }}">
                                            <img src="{{ asset('storage/' . $fp->thumbnail) }}" alt="img"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        </a>
                                    </div>
                                    <div class="gt-recent-content">
                                        <h5>
                                            <a href="{{ route('client.postpublic.show', $fp->id) }}" class="text-decoration-none">
                                                {{ $fp->title }}
                                            </a>
                                        </h5>
                                        <ul>
                                            <li>{{ $fp->created_at->format('M d, Y') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            @empty
                                <p>Không có bài viết nổi bật</p>
                            @endforelse
                        </div>
                    </div>
            
                    <!-- Club Info -->
                    <div class="gt-single-sideber-widget">
                        <div class="gt-widget-title">
                            <h3>Thông tin CLB</h3>
                        </div>
                        <div class="gt-recent-post-area">
                            @if ($selectedClub)
                                <div class="text-center mb-3">
                                    <img src="{{ asset('storage/' . $selectedClub->logo) }}" alt="logo"
                                        style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                                </div>
                                <h4 class="text-center">{{ $selectedClub->name }}</h4>
                                <p class="text-center" style="font-size: 14px;">
                                    {{ Str::limit($selectedClub->description, 120) }}
                                </p>
                                {{-- <ul style="list-style: none; padding-left: 0; font-size: 14px;">
                                    <li><strong>Tổng số bài viết:</strong> {{ $clubStats['total_posts'] ?? 0 }}</li>
                                </ul> --}}
                            @else
                                <p class="text-center">Không có thông tin CLB</p>
                            @endif
                        </div>
                    </div>
            
                </div>
            </div>

            </div>
        </div>
    </div>
</section>