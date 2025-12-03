@extends('client.layouts.app')
@section('content')
        <section class="news-standard-section section-padding">
            <div class="container">
                <div class="gt-news-details-wrapper">
                    <div class="row g-4">
                        <div class="col-12 col-lg-8">
                            <div class="gt-details-image">
        {{-- Ảnh thumbnail của bài viết --}}
    <img src="{{ asset('storage/' . $post->thumbnail) }}" alt="img"
        style="width: 100%; aspect-ratio: 16/9; object-fit: cover; object-position: center; border-radius: 8px;">


    </div>
    <div class="gt-news-details-content">
        {{-- Tiêu đề bài viết --}}
        <h3>{{ $post->title }}</h3>

        {{-- Nội dung bài viết (lưu HTML trong DB) --}}
        <p>
            {!! $post->content !!}
        </p>

                                <!-- <div class="gt-sideber">
                                            <h6 class="mb-0">
                                                "A fantastic tribute to the club’s greatest heroes! I loved how the blog highlighted not just the legendary players but also the coaches and staff who contributed."
                                            </h6>
                                        </div>
                                        <h4 class="news-title">01. Legendary Players Who Shaped the Club</h4>
                                        <p>
                                            jerseys, Hall of Fame inductions, and special events keeps their legacy alive, motivating fans and new generations to carry forward the club’s spirit and values.
                                        </p>

                                        <h4 class="news-title">02. How the Club Honors Its Legends</h4>
                                        <p>Custom-curated itineraries, room preferences remembered, and private concierge services now set the standard for top-tier comfort.</p>

                                        <h4 class="news-title">03. Retiring jersey numbers</h4>
                                        <p>Custom-curated itineraries, room preferences remembered, and private concierge services now set the standard for top-tier comfort.</p>

                                        <h4 class="news-title">04. Statues or murals at the stadium</h4>
                                        <p>Custom-curated itineraries, room preferences remembered, and private concierge services now set the standard for top-tier comfort.</p>

                                        <h4 class="news-title">05. Hall of Fame inductions</h4>
                                        <p>Custom-curated itineraries, room preferences remembered, and private concierge services now set the standard for top-tier comfort.</p>

                                        <h4 class="news-title">06. Culinary Exploration</h4>
                                        <p>Custom-curated itineraries, room preferences remembered, and private concierge services now set the standard for top-tier comfort.</p>

                                        <h4 class="news-title">07. Special events and anniversary matches</h4>
                                        <p>Custom-curated itineraries, room preferences remembered, and private concierge services now set the standard for top-tier comfort.</p>

                                        <h4 class="news-title">08. Extended Stays & Workcations</h4>
                                        <p>Custom-curated itineraries, room preferences remembered, and private concierge services now set the standard for top-tier comfort.</p> -->

                                <!-- <div class="row g-4 mt-3">
                                            <div class="col-lg-6">
                                                <div class="gt-details-image">
                                                    <img src="assets/img/inner/news/post-4.jpg" alt="img">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="gt-details-image">
                                                     <img src="assets/img/inner/news/post-5.jpg" alt="img">
                                                </div>
                                            </div>
                                        </div>
                                        <h3 class="text">Ready for the Ride of a Lifetime?</h3>
                                        <p>
                                            No matter your skill level, these legendary motorcycle routes offer unforgettable experiences. Pack your gear, fuel up, and hit the open road
                                        </p> -->
                                <div class="row gt-tag-share-wrap mt-4 mb-5">
                                    <div class="col-lg-8 col-12">
                                        <div class="tagcloud">
                                            <!-- <span>Tags:</span>                                  
                                                    <a href="news-details.html">MatchDay</a>
                                                    <a href="news-details.html">FinalWhistle</a>
                                                    <a href="news-details.html">Club</a> -->
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-12 mt-3 mt-lg-0 text-lg-end">
                                        <div class="social-share">
                                            <a href="#"><i class="fab fa-twitter"></i></a>
                                            <a href="#"><i class="fa-brands fa-youtube"></i></a>
                                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                                        </div>
                                    </div>
                                </div>
                            <div class="gt-comments-area">
                                <div class="gt-comments-heading">
                                    <h3>{{ $comments->count() }} Comments</h3>
                                </div>

                                @forelse ($comments as $comment)
                                    <div class="gt-blog-single-comment d-flex gap-4 pt-4 pb-4">
                                        <div class="image">
                                            {{-- Avatar user nếu có, fallback ảnh mặc định --}}
                                            <img src="{{ $comment->user->avatar ?? asset('assets/img/inner/news/comment-1.png') }}" alt="img">
                                        </div>
                                        <div class="gt-content">
                                            <div class="head d-flex flex-wrap gap-2 align-items-center justify-content-between">
                                                <div class="con">
                                                    <h5>
                                                        <a href="#">
                                                            {{ $comment->user->name ?? 'Ẩn danh' }}
                                                        </a>
                                                    </h5>
                                                    <span>{{ $comment->created_at->format('F d, Y \a\t h:i a') }}</span>
                                                </div>
                                                <a href="#" class="reply">Reply</a>
                                            </div>
                                            <p class="mt-30 mb-4">
                                                {!! nl2br(e($comment->content)) !!}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <p>Chưa có bình luận nào.</p>
                                @endforelse
                            </div>

                                <div class="gt-comment-form-wrap pt-5">
                                    <h3>Leave a comments</h3>
                                    <form action="#" id="contact-form" method="POST">
                                        <div class="row g-4">
                                            <div class="col-lg-6">
                                                <div class="form-clt">
                                                    <span>Your Name*</span>
                                                    <input type="text" name="name" id="name" placeholder="Your Name">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-clt">
                                                    <span>Your Email*</span>
                                                    <input type="text" name="email" id="email6" placeholder="Your Email">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-clt">
                                                    <span>Message*</span>
                                                    <textarea name="message" id="message"
                                                        placeholder="Type your message"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <button class="theme-btn" type="submit">
                                                    SEND MESSAGE <i class="fa-solid fa-arrow-up-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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

@endsection
