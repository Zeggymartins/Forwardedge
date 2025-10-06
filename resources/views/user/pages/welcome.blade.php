@extends('user.master_page')
@section('title', ' Welcome | Forward Edge Consulting')
@section('main')

        <!-- start: Banner Section -->
        <section class="tj-banner-section section-gap-x">
            <div class="banner-area">
                <div class="banner-left-box">
                    <div class="banner-content">
                        <span class="sub-title wow fadeInDown" data-wow-delay=".2s">
                            <i class="tji-excellence"></i> Recognized for Excellence
                        </span>
                        <h1 class="banner-title title-anim">Where vision meets
                            <span>Direction.</span>
                        </h1>
                        <div class="banner-desc-area wow fadeInUp" data-wow-delay=".7s">
                            <a class="banner-link" href="about.html">
                                <span><i class="tji-arrow-right-big"></i></span>
                            </a>
                            <div class="banner-desc">Do you need expert guidance to transform your business
                                with cutting-edge technology and ensure long-term security and growth?
                            </div>
                        </div>
                    </div>
                    <div class="banner-shape">
                        <img src="{{ asset('frontend/assets/images/shape/pattern-bg.webp') }}" alt="">
                    </div>
                </div>
                <div class="banner-right-box">
                    <div class="banner-img">
                        <img data-speed="0.8" src="{{ asset('frontend/assets/images/hero/banner.jpg') }}" alt="">
                    </div>
                    <!-- <div class="box-area">
                    <div class="customers-box">
                      <div class="customers">
                        <ul>
                          <li class="wow fadeInLeft" data-wow-delay=".5s"><img src="assets/images/testimonial/client-1.webp"
                              alt=""></li>
                          <li class="wow fadeInLeft" data-wow-delay=".6s"><img src="assets/images/testimonial/client-2.webp"
                              alt=""></li>
                          <li class="wow fadeInLeft" data-wow-delay=".7s"><img src="assets/images/testimonial/client-3.webp"
                              alt=""></li>
                          <li class="wow fadeInLeft" data-wow-delay=".8s"><span><i class="tji-plus"></i></span></li>
                        </ul>
                      </div>
                      <div class="customers-number wow fadeInUp" data-wow-delay=".5s">30K</div>
                      <h6 class="customers-text wow fadeInUp" data-wow-delay=".5s">Happy customer we have world-wide.</h6>
                    </div>
                  </div> -->
                </div>
            </div>
            <div class="banner-scroll wow fadeInDown" data-wow-delay="2s">
                <a href="#choose" class="scroll-down">
                    <span><i class="tji-arrow-down-long"></i></span>
                    Scroll Down
                </a>
            </div>
        </section>
        <!-- end: Banner Section -->

        <!-- start: Choose Section -->
        <section id="choose" class="tj-choose-section section-gap">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="sec-heading text-center">
                            <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>Choose the
                                Best</span>
                            <h2 class="sec-title title-anim">Empowering Business with <span>Expertise.</span>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="row row-gap-4 rightSwipeWrap">
                    <div class="col-lg-4">
                        <div class="choose-box right-swipe">
                            <div class="choose-content">
                                <div class="choose-icon">
                                    <i class="tji-innovative"></i>
                                </div>
                                <h4 class="title">Innovative Solutions</h4>
                                <p class="desc">We stay ahead of the curve, leveraging cutting-edge
                                    technologies and strategies to
                                    keep
                                    you competitive in a marketplace.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="choose-box right-swipe">
                            <div class="choose-content">
                                <div class="choose-icon">
                                    <i class="tji-award"></i>
                                </div>
                                <h4 class="title">Award-Winning Expertise</h4>
                                <p class="desc">Recognized by industry leaders, our award-winning team has a
                                    proven record of
                                    delivering
                                    excellence across projects.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="choose-box right-swipe">
                            <div class="choose-content">
                                <div class="choose-icon">
                                    <i class="tji-support"></i>
                                </div>
                                <h4 class="title">Dedicated Support</h4>
                                <p class="desc">Our team is always available to address your concerns,
                                    providing quick and effective
                                    solution to keep your business.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end: Choose Section -->

        <!-- start: Client Section -->
        <section class="tj-client-section client-section-gap wow fadeInUp" data-wow-delay=".4s">
            <div class="container-fluid client-container">
                <div class="row">
                    <div class="col-12">
                        <div class="client-content">
                            <h5 class="sec-title"> <span class="client-numbers">10+</span> Companies
                                affiliated with
                                <span class="client-text">Forward Edge</span> Here
                            </h5>
                        </div>
                        <div class="swiper client-slider client-slider-1">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide client-item">
                                    <div class="client-logo">
                                        <img src="{{ asset('frontend/assets/images/brands/brand-1.webp') }}" alt="">
                                    </div>
                                </div>
                                <div class="swiper-slide client-item">
                                    <div class="client-logo">
                                        <img src="{{ asset('frontend/assets/images/brands/brand-2.webp') }}"
                                            alt="">
                                    </div>
                                </div>
                                <div class="swiper-slide client-item">
                                    <div class="client-logo">
                                        <img src="{{ asset('frontend/assets/images/brands/brand-3.webp') }}"
                                            alt="">
                                    </div>
                                </div>
                                <div class="swiper-slide client-item">
                                    <div class="client-logo">
                                        <img src="{{ asset('frontend/assets/images/brands/brand-4.webp') }}"
                                            alt="">
                                    </div>
                                </div>
                                <div class="swiper-slide client-item">
                                    <div class="client-logo">
                                        <img src="{{ asset('frontend/assets/images/brands/brand-5.webp') }}"
                                            alt="">
                                    </div>
                                </div>
                                <div class="swiper-slide client-item">
                                    <div class="client-logo">
                                        <img src="{{ asset('frontend/assets/images/brands/brand-6.webp') }}"
                                            alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end: Client Section -->

        <!-- start: About Section -->
        <section class="tj-about-section section-gap">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6 col-lg-6 order-lg-1 order-2">
                        <div class="about-img-area wow fadeInLeft" data-wow-delay=".2s">
                            <div class="about-img overflow-hidden">
                                <img data-speed="0.8" src="{{ asset('frontend/assets/images/about/banner1.jpg') }}"
                                    alt="" height="400px">
                            </div>
                            <div class="box-area">
                                <div class="experience-box wow fadeInUp" data-wow-delay=".3s">
                                    <span class="sub-title">Experiences</span>
                                    <div class="customers-number">13+</div>
                                    <h6 class="customers-text">Decades of Experience, Endless Innovation</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 order-lg-2 order-1">
                        <div class="about-content-area style-1 wow fadeInLeft" data-wow-delay=".2s">
                            <div class="sec-heading">
                                <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>Get to
                                    Know
                                    Us</span>
                                <h2 class="sec-title title-anim">Empowering Businesses with Innovation,
                                    Expertise, and for <span>Success.</span>
                                </h2>
                            </div>
                            <div class="wow fadeInUp" data-wow-delay=".5s">
                                <a class="text-btn" href="about.html">
                                    <span class="btn-text"><span>Learn More</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div>
                        </div>
                        <div class="about-bottom-area">
                            <div class="client-review-cont wow fadeInUp" data-wow-delay=".7s">
                                <div class="rating-area">
                                    <div class="star-ratings">
                                        <div class="fill-ratings" style="width: 100%">
                                            <span>★★★★★</span>
                                        </div>
                                        <div class="empty-ratings">
                                            <span>★★★★★</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="desc"> At Forward Edge Consulting Ltd, we provide innovative and
                                    secure technology solutions to businesses of all sizes, across diverse
                                    industries. Whether you’re in finance, healthcare, education, government,
                                    retail, or any other sector, our expertise in digital transformation,
                                    cybersecurity, cloud computing, AI integration, and software development
                                    ensures that your organization stays ahead of the curve.

                                    No matter your industry, we help you harness the power of technology to
                                    enhance operations, protect critical data, and drive growth in an
                                    increasingly digital world.
                                </p>
                                <div class="client-info-area">

                                    <span class="quote-icon"><i class="tji-quote"></i></span>
                                </div>
                            </div>
                            <!-- <div class="video-img  wow fadeInUp" data-wow-delay=".9s">
                        <img src="assets/images/about/about-2.webp" alt="">
                        <a class="video-btn video-popup" data-autoplay="true" data-vbtype="video" data-maxwidth="1200px"
                          href="https://www.youtube.com/watch?v=MLpWrANjFbI&amp;ab_channel=eidelchteinadvogados">
                          <span><i class="tji-play"></i></span>
                        </a>
                      </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end: About Section -->

        <!-- start: Service Section -->
        <section class="tj-service-section overflow-hidden section-gap section-gap-x">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="sec-heading text-center">
                            <span class="sub-title text-white wow fadeInUp" data-wow-delay=".3s"><i
                                    class="tji-box"></i>Our
                                Solutions</span>
                            <h2 class="sec-title text-white title-anim">Solutions to Transform Your
                                <span>Business.</span>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid p-0">
                <div class="row">
                    <div class="col-12">
                        <div class="service-wrapper wow fadeInUp" data-wow-delay=".4s">
                            <div class="swiper service-slider">
                                <div class="swiper-wrapper">

                                    <!-- Cybersecurity -->
                                    <div class="swiper-slide">
                                        <div class="service-item style-1">
                                            <div class="service-img">
                                                <img src="{{ asset('frontend/assets/images/service/service-1.webp') }}"
                                                    alt="Cybersecurity Services">
                                            </div>
                                            <div class="service-icon">
                                                <i class="tji-service-1"></i>
                                            </div>
                                            <div class="service-content">
                                                <h4 class="title"><a href="service-details.html">Cybersecurity</a></h4>
                                                <p class="desc">
                                                    Protect your business from evolving cyber threats with
                                                    advanced firewalls, real-time monitoring, and
                                                    penetration testing that safeguard sensitive data and ensure
                                                    compliance.
                                                </p>
                                                <a class="text-btn" href="service-details.html">
                                                    <span class="btn-text"><span>Learn More</span></span>
                                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Software Development -->
                                    <div class="swiper-slide">
                                        <div class="service-item style-1">
                                            <div class="service-img">
                                                <img src="{{ asset('frontend/assets/images/service/service-5.webp') }}"
                                                    alt="Software Development">
                                            </div>
                                            <div class="service-icon">
                                                <i class="tji-service-2"></i>
                                            </div>
                                            <div class="service-content">
                                                <h4 class="title"><a href="service-details.html">Software
                                                        Development</a></h4>
                                                <p class="desc">
                                                    We design and build scalable web and mobile applications
                                                    tailored to your workflow, helping you automate
                                                    processes, enhance customer engagement, and grow your
                                                    business faster.
                                                </p>
                                                <a class="text-btn" href="service-details.html">
                                                    <span class="btn-text"><span>Learn More</span></span>
                                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cloud Computing -->
                                    <div class="swiper-slide">
                                        <div class="service-item style-1">
                                            <div class="service-img">
                                                <img src="{{ asset('frontend/assets/images/service/service-6.webp') }}"
                                                    alt="Cloud Computing">
                                            </div>
                                            <div class="service-icon">
                                                <i class="tji-service-3"></i>
                                            </div>
                                            <div class="service-content">
                                                <h4 class="title"><a href="service-details.html">Cloud
                                                        Computing</a></h4>
                                                <p class="desc">
                                                    Migrate to secure, flexible cloud platforms that reduce IT
                                                    costs, improve collaboration, and allow your
                                                    team to scale resources instantly without downtime.
                                                </p>
                                                <a class="text-btn" href="service-details.html">
                                                    <span class="btn-text"><span>Learn More</span></span>
                                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Enterprise Network Infrastructure -->
                                    <div class="swiper-slide">
                                        <div class="service-item style-1">
                                            <div class="service-img">
                                                <img src="{{ asset('frontend/assets/images/service/service-7.webp') }}"
                                                    alt="Enterprise Network Infrastructure">
                                            </div>
                                            <div class="service-icon">
                                                <i class="tji-service-4"></i>
                                            </div>
                                            <div class="service-content">
                                                <h4 class="title"><a href="service-details.html">Enterprise
                                                        Network Infrastructure</a></h4>
                                                <p class="desc">
                                                    Build high-performance, secure, and reliable networks with
                                                    enterprise-grade switches, routers, and Wi-Fi
                                                    solutions that keep your business connected at all times.
                                                </p>
                                                <a class="text-btn" href="service-details.html">
                                                    <span class="btn-text"><span>Learn More</span></span>
                                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="swiper-pagination-area white-pagination"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="bg-shape-1">
                <img src="{{ asset('frontend/assets/images/shape/pattern-2.svg') }}" alt="">
            </div>
            <div class="bg-shape-2">
                <img src="{{ asset('frontend/assets/images/shape/pattern-3.svg') }}" alt="">
            </div>
        </section>
        <!-- end: Service Section -->

        <!-- start: Project Section -->
        <section class="tj-project-section section-gap">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="sec-heading-wrap">
                            <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>Proud
                                Projects</span>
                            <div class="heading-wrap-content">
                                <div class="sec-heading">
                                    <h2 class="sec-title title-anim">Breaking Boundaries, Building
                                        <span>Dreams.</span>
                                    </h2>
                                </div>
                                <p class="desc wow fadeInUp" data-wow-delay=".5s">We work closely with our
                                    clients to understand
                                    their
                                    unique needs and craft tailored
                                    solutions that address challenges.</p>
                                <div class="btn-wrap wow fadeInUp" data-wow-delay=".6s">
                                    <a class="tj-primary-btn" href="portfolio.html">
                                        <span class="btn-text"><span>More Projects</span></span>
                                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="project-area tj-arrange-container">
                            <div class="project-item tj-arrange-item">
                                <div class="project-img"
                                    data-bg-image="{{ asset('frontend/assets/images/project/event2.jpg') }}"></div>
                                <div class="project-content">
                                    <span class="categories"><a href="portfolio-details.html">Connect</a></span>
                                    <div class="project-text">
                                        <h4 class="title"><a href="portfolio-details.html">Event Management
                                                Platform</a></h4>
                                        <a class="project-btn" href="portfolio-details.html">
                                            <i class="tji-arrow-right-long"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="project-item tj-arrange-item">
                                <div class="project-img"
                                    data-bg-image="{{ asset('frontend/assets/images/project/picture.jpg') }}"></div>
                                <div class="project-content">
                                    <span class="categories"><a href="portfolio-details.html">Empower</a></span>
                                    <div class="project-text">
                                        <h4 class="title"><a href="portfolio-details.html">Digital
                                                Marketing Campaign</a></h4>
                                        <a class="project-btn" href="portfolio-details.html">
                                            <i class="tji-arrow-right-long"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="project-item tj-arrange-item">
                                <div class="project-img"
                                    data-bg-image="{{ asset('frontend/assets/images/project/picture3.jpg') }}"></div>
                                <div class="project-content">
                                    <span class="categories"><a href="portfolio-details.html">Support</a></span>
                                    <div class="project-text">
                                        <h4 class="title"><a href="portfolio-details.html">Interactive
                                                Learning Platform</a></h4>
                                        <a class="project-btn" href="portfolio-details.html">
                                            <i class="tji-arrow-right-long"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="project-item tj-arrange-item">
                                <div class="project-img"
                                    data-bg-image="{{ asset('frontend/assets/images/project/eventbanner.jpg') }}"></div>
                                <div class="project-content">
                                    <span class="categories"><a href="portfolio-details.html">Business</a></span>
                                    <div class="project-text">
                                        <h4 class="title"><a href="portfolio-details.html">Environmental
                                                Impact Dashboard</a></h4>
                                        <a class="project-btn" href="portfolio-details.html">
                                            <i class="tji-arrow-right-long"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end: Project Section -->

        <!-- start: Countup Section -->
        <div class="tj-countup-section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="countup-wrap">
                            <div class="countup-item">
                                <div class="inline-content">
                                    <span class="odometer countup-number" data-count="93"></span>
                                    <span class="count-plus">%</span>
                                </div>
                                <span class="count-text">Projects Completed.</span>
                                <span class="count-separator"
                                    data-bg-image="{{ asset('frontend/assets/images/shape/separator.svg') }}"></span>
                            </div>
                            <div class="countup-item">
                                <div class="inline-content">
                                    <span class="odometer countup-number" data-count="20"></span>
                                    <span class="count-plus">M</span>
                                </div>
                                <span class="count-text">Reach Worldwide</span>
                                <span class="count-separator"
                                    data-bg-image="{{ asset('frontend/assets/images/shape/separator.svg') }}"></span>
                            </div>
                            <div class="countup-item">
                                <div class="inline-content">
                                    <span class="odometer countup-number" data-count="8.5"></span>
                                    <span class="count-plus">X</span>
                                </div>
                                <span class="count-text">Faster Growth</span>
                                <span class="count-separator"
                                    data-bg-image="{{ asset('frontend/assets/images/shape/separator.svg') }}"></span>
                            </div>
                            <div class="countup-item">
                                <div class="inline-content">
                                    <span class="odometer countup-number" data-count="100"></span>
                                    <span class="count-plus">+</span>
                                </div>
                                <span class="count-text">Awards Archived</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end: Countup Section -->

        <!-- start: Testimonial Section -->
        <section class="tj-testimonial-section section-gap section-gap-x">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-12">
                        <div class="sec-heading-wrap">
                            <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>Clients
                                Feedback</span>
                            <div class="heading-wrap-content">
                                <div class="sec-heading">
                                    <h2 class="sec-title title-anim">Success <span>Stories</span> Fuel our
                                        Innovation.</h2>
                                </div>
                                <div class="slider-navigation d-inline-flex wow fadeInUp" data-wow-delay=".4s">
                                    <div class="slider-prev">
                                        <span class="anim-icon">
                                            <i class="tji-arrow-left"></i>
                                            <i class="tji-arrow-left"></i>
                                        </span>
                                    </div>
                                    <div class="slider-next">
                                        <span class="anim-icon">
                                            <i class="tji-arrow-right"></i>
                                            <i class="tji-arrow-right"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="testimonial-wrapper wow fadeInUp" data-wow-delay=".5s">
                            <div class="swiper swiper-container testimonial-slider">
                                <div class="swiper-wrapper d-flex align-items-stretch"> <!-- flex here -->

                                    <div class="swiper-slide d-flex"> <!-- slide flex -->
                                        <div
                                            class="testimonial-item d-flex flex-column justify-content-between h-100 p-3 border rounded shadow-sm">
                                            <span class="quote-icon"><i class="tji-quote"></i></span>
                                            <div class="desc">
                                                <p>Working with Forward Edge Consulting has been so rewarding.
                                                    About a year ago I needed to understand more about Crypto
                                                    Currencies and Stocks, I was well trained and shown
                                                    strategic ways on how to deal on these financial assets.</p>
                                            </div>
                                            <div class="testimonial-author mt-auto">
                                                <div class="author-inner d-flex align-items-center">
                                                    <div class="author-img me-2">
                                                        <img src="{{ asset('frontend/assets/images/testimonial/client-1.webp') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="author-header">
                                                        <h4 class="title mb-0">Chris Davies</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="swiper-slide d-flex">
                                        <div
                                            class="testimonial-item d-flex flex-column justify-content-between h-100 p-3 border rounded shadow-sm">
                                            <span class="quote-icon"><i class="tji-quote"></i></span>
                                            <div class="desc">
                                                <p>Attending a workshop organized by Forward Edge Consulting Ltd
                                                    changed my orientation about Real Estate and opened my eyes
                                                    to see and better opportunities in the industry.</p>
                                            </div>
                                            <div class="testimonial-author mt-auto">
                                                <div class="author-inner d-flex align-items-center">
                                                    <div class="author-img me-2">
                                                        <img src="{{ asset('frontend/assets/images/testimonial/client-2.webp') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="author-header">
                                                        <h4 class="title mb-0">Ebuwa Ahusimere</h4>
                                                        <span class="designation">Petrokrafte Nigeria</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="swiper-slide d-flex">
                                        <div
                                            class="testimonial-item d-flex flex-column justify-content-between h-100 p-3 border rounded shadow-sm">
                                            <span class="quote-icon"><i class="tji-quote"></i></span>
                                            <div class="desc">
                                                <p>Forward Edge Consulting really helped me understand so much
                                                    about Real Estate. I have been trying my best to get myself
                                                    in the industry but always felt it was so difficult and too
                                                    capital intensive. I took one of their Course and I have
                                                    started my journey with almost no money and it’s been a
                                                    great experience.</p>
                                            </div>
                                            <div class="testimonial-author mt-auto">
                                                <div class="author-inner d-flex align-items-center">
                                                    <div class="author-img me-2">
                                                        <img src="{{ asset('frontend/assets/images/testimonial/client-3.webp') }}"
                                                            alt="">
                                                    </div>
                                                    <div class="author-header">
                                                        <h4 class="title mb-0">Joshua Odubu</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="swiper-pagination-area"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-shape-1">
                <img src="{{ asset('frontend/assets/images/shape/pattern-2.svg') }}" alt="">
            </div>
            <div class="bg-shape-2">
                <img src="{{ asset('frontend/assets/images/shape/pattern-3.svg') }}" alt="">
            </div>
        </section>
        <!-- end: Testimonial Section -->

        <!-- start: Faq Section -->
        <section class="tj-faq-section section-gap tj-arrange-container-2">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-6">
                        <div class="faq-img-area tj-arrange-item-2">
                            <div class="faq-img overflow-hidden">
                                <img src="{{ asset('frontend/assets/images/faq/pic.jpg') }}" alt="">
                                <h2 class="title">Need Help? Start Here...</h2>
                            </div>
                            <div class="box-area ">
                                <div class="call-box">
                                    <h4 class="title">Get Started Free Call? </h4>
                                    <span class="call-icon"><i class="tji-phone"></i></span>
                                    <a class="number" href="tel:18884521505"><span>1-888-452-1505</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="accordion tj-faq tj-arrange-item-2" id="faqOne">
                            <div class="accordion-item active">
                                <button class=" faq-title" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq-1" aria-expanded="true">What services does Bexon
                                    offer to clients?</button>
                                <div id="faq-1" class="collapse show" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Getting started is easy! Simply reach out to us through our contact
                                            form or give us a call,
                                            and
                                            we’ll schedule a consultation to discuss your project and how we can
                                            best assist you. Our team
                                            keeps you informed throughout the process, ensuring quality control
                                            and timely delivery.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq-2" aria-expanded="false">How do I get started with Corporate
                                    Business?</button>
                                <div id="faq-2" class="collapse" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Getting started is easy! Simply reach out to us through our contact
                                            form or give us a call,
                                            and
                                            we’ll schedule a consultation to discuss your project and how we can
                                            best assist you. Our team
                                            keeps you informed throughout the process, ensuring quality control
                                            and timely delivery.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq-3" aria-expanded="false">How do you ensure the success of a
                                    project?</button>
                                <div id="faq-3" class="collapse" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Getting started is easy! Simply reach out to us through our contact
                                            form or give us a call,
                                            and
                                            we’ll schedule a consultation to discuss your project and how we can
                                            best assist you. Our team
                                            keeps you informed throughout the process, ensuring quality control
                                            and timely delivery.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq-4" aria-expanded="false">How long will it take to complete my
                                    project?</button>
                                <div id="faq-4" class="collapse" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Getting started is easy! Simply reach out to us through our contact
                                            form or give us a call,
                                            and
                                            we’ll schedule a consultation to discuss your project and how we can
                                            best assist you. Our team
                                            keeps you informed throughout the process, ensuring quality control
                                            and timely delivery.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq-5" aria-expanded="false">Can I track the progress of my
                                    project?</button>
                                <div id="faq-5" class="collapse" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Getting started is easy! Simply reach out to us through our contact
                                            form or give us a call,
                                            and
                                            we’ll schedule a consultation to discuss your project and how we can
                                            best assist you. Our team
                                            keeps you informed throughout the process, ensuring quality control
                                            and timely delivery.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end: Faq Section -->

        <!-- start: Contact Section -->
        <section class="tj-contact-section section-gap section-gap-x">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="global-map wow fadeInUp" data-wow-delay=".3s">
                            <div class="global-map-img">
                                <img src="{{ asset('frontend/assets/images/bg/map.svg') }}" alt="Image">
                                <div class="location-indicator loc-1">
                                    <div class="location-tooltip">
                                        <span>Head office:</span>
                                        <p>993 Renner Burg, West Rond, MT 94251-030, USA.</p>
                                        <a href="tel:10095447818">P: +1 (009) 544-7818</a>
                                        <a href="mailto:support@bexon.com">M: support@bexon.com</a>
                                    </div>
                                </div>
                                <div class="location-indicator loc-2">
                                    <div class="location-tooltip">
                                        <span>Regional office:</span>
                                        <p>Hessisch Lichtenau 37235, Kassel, Germany.</p>
                                        <a href="tel:10098801810">P: +1 (009) 880-1810</a>
                                        <a href="mailto:support@bexon.com">M: support@bexon.com</a>
                                    </div>
                                </div>
                                <div class="location-indicator loc-3">
                                    <div class="location-tooltip">
                                        <span>Regional office:</span>
                                        <p>32 Altamira, State of Pará, Brazil.</p>
                                        <a href="tel:10095447818">P: +1 (009) 544-7818</a>
                                        <a href="mailto:support@bexon.com">M: support@bexon.com</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="contact-form style-2 wow fadeInUp" data-wow-delay=".4s">
                            <div class="sec-heading">
                                <span class="sub-title text-white"><i class="tji-box"></i>Get in
                                    Touch</span>
                                <h2 class="sec-title title-anim">Drop Us a <span>Line.</span></h2>
                            </div>
                            <form id="contact-form-2">
                                <div class="row wow fadeInUp" data-wow-delay=".5s">
                                    <div class="col-sm-6">
                                        <div class="form-input">
                                            <input type="text" name="cfName2" placeholder="Full Name *">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-input">
                                            <input type="email" name="cfEmail2" placeholder="Email Address *">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-input">
                                            <input type="tel" name="cfPhone2" placeholder="Phone number *">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-input">
                                            <div class="tj-nice-select-box">
                                                <div class="tj-select">
                                                    <select name="cfSubject2">
                                                        <option value="0">Chose a option</option>
                                                        <option value="1">Business Strategy</option>
                                                        <option value="2">Customer Experience</option>
                                                        <option value="3">Sustainability and ESG</option>
                                                        <option value="4">Training and Development
                                                        </option>
                                                        <option value="5">IT Support & Maintenance
                                                        </option>
                                                        <option value="6">Marketing Strategy</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-input message-input">
                                            <textarea name="cfMessage2" id="message" placeholder="Type message *"></textarea>
                                        </div>
                                    </div>
                                    <div class="submit-btn">
                                        <button class="tj-primary-btn" type="submit">
                                            <span class="btn-text"><span>Send Message</span></span>
                                            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-shape-1">
                <img src="{{ asset('frontend/assets/images/shape/pattern-2.svg') }}" alt="">
            </div>
            <div class="bg-shape-2">
                <img src="{{ asset('frontend/assets/images/shape/pattern-3.svg') }}" alt="">
            </div>
        </section>
        <!-- end: Contact Section -->

        <!-- start: Blog Section -->
        <section class="tj-blog-section section-gap">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="sec-heading text-center">
                            <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>Insights &
                                Ideas</span>
                            <h2 class="sec-title title-anim">The Ultimate <span>Resource.</span></h2>
                        </div>
                    </div>
                </div>
                <div class="row row-gap-4">
                    <div class="col-xl-4 col-md-6">
                        <div class="blog-item wow fadeInUp" data-wow-delay=".4s">
                            <div class="blog-thumb">
                                <a href="blog-details.html"><img
                                        src="{{ asset('frontend/assets/images/blog/blog-1.webp') }}" alt=""></a>
                                <div class="blog-date">
                                    <span class="date">28</span>
                                    <span class="month">Feb</span>
                                </div>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <span class="categories"><a href="blog-details.html">Business</a></span>
                                    <span>By <a href="blog-details.html">Ellinien Loma</a></span>
                                </div>
                                <h4 class="title"><a href="blog-details.html">Innovative Solutions for
                                        every Business Success.</a>
                                </h4>
                                <a class="text-btn" href="blog-details.html">
                                    <span class="btn-text"><span>Read More</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="blog-item wow fadeInUp" data-wow-delay=".4s">
                            <div class="blog-thumb">
                                <a href="blog-details.html"><img
                                        src="{{ asset('frontend/assets/images/blog/blog-2.webp') }}" alt=""></a>
                                <div class="blog-date">
                                    <span class="date">28</span>
                                    <span class="month">Feb</span>
                                </div>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <span class="categories"><a href="blog-details.html">Business</a></span>
                                    <span>By <a href="blog-details.html">Ellinien Loma</a></span>
                                </div>
                                <h4 class="title"><a href="blog-details.html">Harnessing Digital Transform
                                        a Roadmap Businesses.</a>
                                </h4>
                                <a class="text-btn" href="blog-details.html">
                                    <span class="btn-text"><span>Read More</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-6">
                        <div class="blog-item wow fadeInUp" data-wow-delay=".4s">
                            <div class="blog-thumb">
                                <a href="blog-details.html"><img
                                        src="{{ asset('frontend/assets/images/blog/blog-3.webp') }}" alt=""></a>
                                <div class="blog-date">
                                    <span class="date">28</span>
                                    <span class="month">Feb</span>
                                </div>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <span class="categories"><a href="blog-details.html">Business</a></span>
                                    <span>By <a href="blog-details.html">Ellinien Loma</a></span>
                                </div>
                                <h4 class="title"><a href="blog-details.html">Mastering Change Management
                                        Lessons for
                                        Businesses.</a>
                                </h4>
                                <a class="text-btn" href="blog-details.html">
                                    <span class="btn-text"><span>Read More</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end: Blog Section -->

      
@endsection
