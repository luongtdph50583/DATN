@php($assetBase = asset('assets1'))
  <footer class="footer-section-2 fix bg-cover" style="background-image: url('{{ $assetBase }}/img/home-2/footer-bg.jpg');">
            <div class="container">
                <div class="footer-instagram-wrapper wow fadeInUp" data-wow-delay=".3s">
                    <div class="swiper footer-instagram-slider">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="footer-instagram-image">
                                    <img src="{{ $assetBase }}/img/home-2/instagram/insta-1.jpg" alt="Instagram 1">
                                    <a href="#" class="gt-icon">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="footer-instagram-image">
                                    <img src="{{ $assetBase }}/img/home-2/instagram/insta-2.jpg" alt="Instagram 2">
                                    <a href="#" class="gt-icon">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="footer-instagram-image">
                                    <img src="{{ $assetBase }}/img/home-2/instagram/insta-3.jpg" alt="Instagram 3">
                                    <a href="#" class="gt-icon">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="footer-instagram-image">
                                    <img src="{{ $assetBase }}/img/home-2/instagram/insta-4.jpg" alt="Instagram 4">
                                    <a href="#" class="gt-icon">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="footer-instagram-image">
                                    <img src="{{ $assetBase }}/img/home-2/instagram/insta-5.jpg" alt="Instagram 5">
                                    <a href="#" class="gt-icon">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="footer-instagram-image">
                                    <img src="{{ $assetBase }}/img/home-2/instagram/insta-6.jpg" alt="Instagram 6">
                                    <a href="#" class="gt-icon">
                                            <i class="fa-brands fa-instagram"></i>
                                        </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-logo-item wow fadeInUp" data-wow-delay=".5s">
                    <img src="{{ $assetBase }}/img/home-2/left-gradient.png" alt="Gradient" class="border-img">
                    <a href="#">
                        <img src="{{ $assetBase }}/img/home-2/logo.svg" alt="{{ config('app.name') }}">
                    </a>
                    <img src="{{ $assetBase }}/img/home-2/right-gradient.png" alt="Gradient" class="border-img">
                </div>
                <div class="footer-widget-wrapper">
                    <div class="row justify-content-between">
                        <div class="col-xl-4 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".2s">
                            <div class="footer-widget-items">
                                <div class="widget-head">
                                    <h3>
                                       About NITRD
                                    </h3>
                                </div>
                                <div class="footer-content">
                                    <p>
                                       National Basketball Arena : Tymon Park, <br> Dublin, Ireland D24 N449 (Map)
                                    </p>
                                    <ul class="footer-contact-list mt-4">
                                    <li>
                                       <a href="tel:+12318005678990"> + (123) 1800-567-8990</a>
                                    </li>
                                    <li>
                                        <a href="mailto:info@nitroexplor.com">
                                           info@nitroexplor.com
                                        </a>
                                    </li>
                                </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-6 wow fadeInUp" data-wow-delay=".4s">
                            <div class="footer-widget-items">
                                <div class="widget-head">
                                    <h3>
                                       About NITRD
                                    </h3>
                                </div>
                                <ul class="list-area">
                                    <li>
                                        <a href="about.html">About us</a>
                                    </li>
                                     <li>
                                        <a href="team.html">National Teams</a>
                                    </li>
                                    <li>
                                        <a href="about.html">Governance</a>
                                    </li>
                                    <li>
                                        <a href="about.html">Our Partners</a>
                                    </li>
                                    <li>
                                        <a href="news.html">blog post</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                       <div class="col-xl-2 col-lg-4 col-md-4 wow fadeInUp" data-wow-delay=".6s">
                            <div class="footer-widget-items">
                                <div class="widget-head">
                                    <h3>
                                      Get Involved
                                    </h3>
                                </div>
                                <ul class="list-area">
                                    <li>
                                        <a href="club-ranking.html">Play</a>
                                    </li>
                                    <li>
                                        <a href="club-ranking.html">National Teams</a>
                                    </li>
                                    <li>
                                        <a href="club-ranking.html">Coach</a>
                                    </li>
                                    <li>
                                        <a href="about.html">League History</a>
                                    </li>
                                    <li>
                                        <a href="club-ranking.html">National Champs</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-8 wow fadeInUp" data-wow-delay=".8s">
                            <div class="footer-widget-items">
                                <div class="widget-head">
                                    <h3>
                                       Newsletter Signup
                                    </h3>
                                </div>
                                <div class="footer-form">
                                    <form action="#">
                                        <input type="text" placeholder="Your Email Address">
                                        <button class="theme-btn" type="submit">
                                            Subscribe <i class="fa-solid fa-arrow-up-right"></i>
                                        </button>
                                    </form>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            I agree to the <a href="contact.html">Privacy Policy.</a>
                                        </label>
                                    </div>
                                    <div class="social-icon d-flex align-items-center">
                                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#"><i class="fab fa-twitter"></i></a>
                                        <a href="#"><i class="fab fa-youtube"></i></a>
                                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-bottom-2 wow fadeInUp" data-wow-delay=".3s">
                 <p>
                    © 2025<b> NITRO</b>. All Rights Reserved.
                </p>
            </div>
        </footer>
