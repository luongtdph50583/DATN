@php($assetBase = asset('assets1'))
<footer class="footer-section-2 fix bg-cover" style="background-image: url('{{ $assetBase }}/img/home-2/footer-bg.jpg');">
    <div class="container">

        {{-- Instagram preview --}}
        <div class="footer-instagram-wrapper wow fadeInUp" data-wow-delay=".3s">
            <div class="swiper footer-instagram-slider">
                <div class="swiper-wrapper">

                    @for($i=1;$i<=6;$i++)
                        <div class="swiper-slide">
                            <div class="footer-instagram-image">
                                <img src="{{ $assetBase }}/img/home-2/instagram/insta-{{ $i }}.jpg" alt="Instagram {{ $i }}">
                                <a href="{{ route('client.home') }}" class="gt-icon">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    @endfor

                </div>
            </div>
        </div>

        {{-- Logo --}}
        <div class="footer-logo-item wow fadeInUp" data-wow-delay=".5s">
            <img src="{{ $assetBase }}/img/home-2/left-gradient.png" class="border-img" alt="">
            <a href="{{ route('client.home') }}">
                <img src="{{ $assetBase }}/img/home-2/logo.svg" alt="Trang chủ">
            </a>
            <img src="{{ $assetBase }}/img/home-2/right-gradient.png" class="border-img" alt="">
        </div>

        {{-- Widgets --}}
        <div class="footer-widget-wrapper">
            <div class="row justify-content-between">

                {{-- About --}}
                <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                    <div class="footer-widget-items">
                        <div class="widget-head">
                            <h3>Giới thiệu</h3>
                        </div>

                        <div class="footer-content">
                            <p>
                                Website quản lý & hoạt động câu lạc bộ sinh viên.
                                <br>
                                Nơi kết nối – học hỏi – phát triển.
                            </p>

                            <ul class="footer-contact-list mt-4">
                                <li>
                                    📍 Trường CĐ FPT Polytechnic
                                </li>
                                <li>
                                    📞 <a href="tel:+84000000000">+84 000 000 000</a>
                                </li>
                                <li>
                                    📧 <a href="mailto:club@gmail.com">club@gmail.com</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Menu --}}
                <div class="col-xl-2 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                    <div class="footer-widget-items">
                        <div class="widget-head">
                            <h3>Liên kết</h3>
                        </div>

                        <ul class="list-area">
                            <li><a href="{{ route('client.home') }}">Trang chủ</a></li>
                            <li><a href="#">Giới thiệu CLB</a></li>
                            <li><a href="#">Sự kiện</a></li>
                            <li><a href="#">Blog</a></li>
                            <li><a href="#">Liên hệ</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Join --}}
                <div class="col-xl-2 col-lg-4 col-md-4 wow fadeInUp" data-wow-delay=".6s">
                    <div class="footer-widget-items">
                        <div class="widget-head">
                            <h3>Tham gia cùng chúng tôi</h3>
                        </div>

                        <ul class="list-area">
                            <li><a href="#">Đăng ký thành viên</a></li>
                            <li><a href="#">Ban tổ chức</a></li>
                            <li><a href="#">Cộng tác viên</a></li>
                            <li><a href="#">Sự kiện CLB</a></li>
                            <li><a href="#">Cựu thành viên</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Newsletter --}}
                <div class="col-xl-4 col-lg-6 col-md-8 wow fadeInUp" data-wow-delay=".8s">
                    <div class="footer-widget-items">
                        <div class="widget-head">
                            <h3>Nhận thông báo</h3>
                        </div>

                        <div class="footer-form">
                            <form action="#">
                                <input type="text" placeholder="Nhập email của bạn">
                                <button class="theme-btn" type="submit">
                                    Đăng ký <i class="fa-solid fa-arrow-up-right"></i>
                                </button>
                            </form>

                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="privacyCheck">
                                <label class="form-check-label" for="privacyCheck">
                                    Tôi đồng ý với <a href="#">chính sách bảo mật</a>.
                                </label>
                            </div>

                            <div class="social-icon d-flex align-items-center">
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-youtube"></i></a>
                                <a href="#"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Bottom --}}
    <div class="footer-bottom-2 wow fadeInUp" data-wow-delay=".3s">
        <p>
            © {{ date('Y') }} <b></b> — Thực hiện bởi sinh viên ❤
        </p>
    </div>

</footer>
