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
                        <div class="banner-desc">Cybersecurity, IT & Renewable Energy Solutions for a Secure and Resilient
                            Future.
                        </div>
                    </div>
                </div>
                <div class="banner-shape">
                    <img src="{{ asset('frontend/assets/images/shape/pattern-bg.webp') }}" alt="">
                </div>
            </div>
            <div class="banner-right-box">
                <div class="banner-img">
                    <img data-speed="0.8" src="{{ asset('frontend/assets/images/hero/banner.png') }}" alt="">
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
                                <div class="clien">
                                    <img src="{{ asset('frontend/assets/images/brands/download.jpg') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/download1.png') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/download2.png') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/download3.png') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/downloa4.jpg') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/download5.png') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/download6.jpg') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/download7.png') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/download8.jpg') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/download9.png') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/download10.png') }}"
                                        alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/skyhigh11.jpg') }}" alt="">
                                </div>
                            </div>
                            <div class="swiper-slide client-item">
                                <div class="">
                                    <img src="{{ asset('frontend/assets/images/brands/download12.png') }}"
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
                            <a class="text-btn" href="{{ route('about') }}">
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
                            <p class="desc"> Forward Edge Consulting is a technology and security consulting firm helping
                                organizations across Africa build resilience through cybersecurity, cloud solutions, AI
                                integration, and enterprise IT infrastructure. In addition to our digital transformation
                                services, we now provide renewable energy solutions — including solar and inverter
                                installations — empowering businesses and communities to achieve both digital and energy
                                resilience.
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
                        <span class="sub-title text-white wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>Our
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

                                @foreach ($services as $service)
                                    <div class="swiper-slide">
                                        <div class="service-item style-1">
                                            <div class="service-img">
                                                <img src="{{ asset('storage/' . $service->thumbnail) }}"
                                                    alt="{{ $service->title }}">
                                            </div>
                                            <div class="service-icon"> <i class="tji-service-1"></i> </div>
                                            <div class="service-content">
                                                <h4 class="title">
                                                    <a href="{{ route('services.show', $service->slug) }}">
                                                        {{ $service->title }}
                                                    </a>
                                                </h4>
                                                <p class="desc">
                                                    {{ Str::limit($service->brief_description, 180) }}
                                                </p>
                                                <a class="text-btn" href="{{ route('services.show', $service->slug) }}">
                                                    <span class="btn-text"><span>Learn More</span></span>
                                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

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
                                <h2 class="sec-title title-anim">Driving Impact Through Real
                                    <span>Projects.</span>
                                </h2>
                            </div>
                            <p class="desc wow fadeInUp" data-wow-delay=".5s">Every project we deliver tells a story of
                                resilience, transformation, and progress. From strengthening organizations against cyber
                                threats to deploying renewable energy solutions and enabling digital transformation, we are
                                proud to create lasting value for our clients and communities.</p>
                            {{-- <div class="btn-wrap wow fadeInUp" data-wow-delay=".6s">
                                <a class="tj-primary-btn" href="{{ route('gallery') }}">
                                    <span class="btn-text"><span>More Projects</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="project-area tj-arrange-container">
                        <div class="project-item tj-arrange-item">
                            <div class="project-img"
                                data-bg-image="{{ asset('frontend/assets/images/project/pic1.jpg') }}"></div>
                            <div class="project-content">
                                <span class="categories">Connect</span>
                                <div class="project-text">
                                    <h4 class="title"><a href="">Building Cyber Resilience </a></h4>
                                    <p class="desc" style="color:white;">Helping organizations protect critical assets
                                        and stay ahead of evolving threats.</p>
                                    <a class="project-btn" href="">
                                        <i class="tji-arrow-right-long"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="project-item tj-arrange-item">
                            <div class="project-img"
                                data-bg-image="{{ asset('frontend/assets/images/project/pic2.jpg') }}"></div>
                            <div class="project-content">
                                <span class="categories"><a href="">Empower</a></span>
                                <div class="project-text">
                                    <h4 class="title"><a href="">Developing the Cyber Workforce </a></h4>
                                    <p class="desc" style="color:white;">Equipping professionals and teams with hands-on
                                        training and compliance skills.
                                    </p>
                                    <a class="project-btn" href="">
                                        <i class="tji-arrow-right-long"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="project-item tj-arrange-item">
                            <div class="project-img"
                                data-bg-image="{{ asset('frontend/assets/images/project/picture3.jpg') }}"></div>
                            <div class="project-content">
                                <span class="categories"><a href="">Support</a></span>
                                <div class="project-text">
                                    <h4 class="title"><a href="">Enabling Digital Transformation </a></h4>
                                    <p class="desc" style="color:white;">Delivering secure IT, infrastructure, and
                                        technology solutions that drive efficiency.</p>
                                    <a class="project-btn" href="">
                                        <i class="tji-arrow-right-long"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="project-item tj-arrange-item">
                            <div class="project-img"
                                data-bg-image="{{ asset('frontend/assets/images/project/solar.jpg') }}"></div>
                            <div class="project-content">
                                <span class="categories"><a href="">Business</a></span>
                                <div class="project-text">
                                    <h4 class="title"><a href="">Expanding Renewable Energy Access</a></h4>
                                    <p class="desc" style="color:white;">Providing solar and inverter systems that
                                        ensure reliable and sustainable power.</p>
                                    <a class="project-btn" href="">
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

                                <!-- Testimonial 1: Cybersecurity & Compliance -->
                                <div class="swiper-slide d-flex">
                                    <div
                                        class="testimonial-item d-flex flex-column justify-content-between h-100 p-4 border border-info rounded-lg shadow-xl bg-gray-900 text-white">
                                        <span class="quote-icon text-info"><i class="tji-quote"></i></span>
                                        <div class="flex-grow-1 mb-3">
                                            <p class="desc" style="color:black;">ForwardEdge completely transformed our security posture from reactive to
                                                proactive. Their ZTA implementation and penetration testing uncovered gaps
                                                we didn't know existed. We now have a truly resilient system integrated with
                                                SIEM/SOAR, giving us confidence in our compliance and defense against
                                                advanced threats.</p>
                                        </div>
                                        <div class="testimonial-author mt-auto">
                                            <div class="author-inner d-flex align-items-center">
                                                <div class="author-img me-2">
                                                    <img src="{{ asset('frontend/assets/images/testimonial/client1.jpeg') }}"
                                                        alt="Client 1">
                                                </div>
                                                <div class="author-header">
                                                    <h4 class="title mb-0">Joshua Odubu</h4>
                                                    {{-- <span class="designation text-secondary">CTO, FinTech
                                                        Innovations</span> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Testimonial 2: Cloud & FinOps -->
                                <div class="swiper-slide d-flex">
                                    <div
                                        class="testimonial-item d-flex flex-column justify-content-between h-100 p-4 border border-info rounded-lg shadow-xl bg-gray-900 text-white">
                                        <span class="quote-icon text-info"><i class="tji-quote"></i></span>
                                        <div class="desc flex-grow-1 mb-3">
                                            <p style="color:black;">We challenged ForwardEdge to optimize our massive cloud spend. Their team
                                                didn't just migrate us; they implemented a rigorous FinOps and IaC strategy,
                                                resulting in a **35% reduction in monthly cloud costs** within six months.
                                                The performance improvements and stability provided by their governance
                                                model were equally impactful.</p>
                                        </div>
                                        <div class="testimonial-author mt-auto">
                                            <div class="author-inner d-flex align-items-center">
                                                <div class="author-img me-2">
                                                    <img src="{{ asset('frontend/assets/images/testimonial/client2.jpeg') }}"
                                                        alt="Client 2">
                                                </div>
                                                <div class="author-header">
                                                    <h4 class="title mb-0">Chris Davies</h4>
                                                    {{-- <span class="designation text-secondary">VP of Infrastructure, Global
                                                        Manufacturing</span> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Testimonial 3: Software Development (Matching your selected text) -->
                                <div class="swiper-slide d-flex">
                                    <div
                                        class="testimonial-item d-flex flex-column justify-content-between h-100 p-4 border border-info rounded-lg shadow-xl bg-gray-900 text-white">
                                        <span class="quote-icon text-info"><i class="tji-quote"></i></span>
                                        <div class="desc flex-grow-1 mb-3">
                                            <p style="color:black;">The team’s expertise in **Domain-Driven Design** and **Microservices** was
                                                clear from the start. They delivered a complex platform on time using their
                                                hyper-transparent Agile process. The resulting product is incredibly
                                                scalable and our in-house team is now better equipped, thanks to their focus
                                                on High-Performance Engineering.</p>
                                        </div>
                                        <div class="testimonial-author mt-auto">
                                            <div class="author-inner d-flex align-items-center">
                                                <div class="author-img me-2">
                                                    <img src="{{ asset('frontend/assets/images/testimonial/client3.jpeg') }}"
                                                        alt="Client 3">
                                                </div>
                                                <div class="author-header">
                                                    <h4 class="title mb-0">Ebuwa Ahusimer</h4>
                                                    <span class="designation text-secondary">Petrokrafte Nigeria</span>
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
                                <a class="number" href="tel:+2347039955591"><span>+2347039955591</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="accordion tj-faq tj-arrange-item-2" id="faqOne">
                        @if ($faqs->count())
                            @foreach ($faqs as $index => $faq)
                                <div class="accordion-item {{ $index === 0 ? 'active' : '' }}">
                                    <button class="faq-title {{ $index === 0 ? '' : 'collapsed' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq-{{ $index + 1 }}"
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                        {{ $faq->question }}
                                    </button>
                                    <div id="faq-{{ $index + 1 }}" class="collapse {{ $index === 0 ? 'show' : '' }}"
                                        data-bs-parent="#faqOne">
                                        <div class="accordion-body faq-text">
                                            {!! $faq->answer !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @php
                                $dummyFaqs = [
                                    [
                                        'question' => 'What services does Bexon offer to clients?',
                                        'answer' =>
                                            'Getting started is easy! Simply reach out to us through our contact form or give us a call, and we’ll schedule a consultation to discuss your project and how we can best assist you. Our team keeps you informed throughout the process, ensuring quality control and timely delivery.',
                                    ],
                                    [
                                        'question' => 'How do I get started with Corporate Business?',
                                        'answer' =>
                                            'Getting started is easy! Simply reach out to us through our contact form or give us a call, and we’ll schedule a consultation to discuss your project and how we can best assist you.',
                                    ],
                                    [
                                        'question' => 'How do you ensure the success of a project?',
                                        'answer' =>
                                            'We ensure success by detailed planning, constant updates, and keeping you informed throughout the process.',
                                    ],
                                    [
                                        'question' => 'How long will it take to complete my project?',
                                        'answer' =>
                                            'The timeline depends on project scope, but we provide realistic estimates and regular updates.',
                                    ],
                                    [
                                        'question' => 'Can I track the progress of my project?',
                                        'answer' =>
                                            'Yes, our team provides progress reports and status updates so you are always informed.',
                                    ],
                                ];
                            @endphp

                            @foreach ($dummyFaqs as $index => $faq)
                                <div class="accordion-item {{ $index === 0 ? 'active' : '' }}">
                                    <button class="faq-title {{ $index === 0 ? '' : 'collapsed' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#faq-dummy-{{ $index + 1 }}"
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                        {{ $faq['question'] }}
                                    </button>
                                    <div id="faq-dummy-{{ $index + 1 }}"
                                        class="collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqOne">
                                        <div class="accordion-body faq-text">
                                            {!! $faq['answer'] !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
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
                        @include('user.partials.form-alerts')
                        <form id="contact-form-2" action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            <div class="row wow fadeInUp" data-wow-delay=".5s">
                                <div class="col-sm-6">
                                    <div class="form-input">
                                        <input type="text" name="cfName2" placeholder="Full Name *"
                                            value="{{ old('cfName2') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-input">
                                        <input type="email" name="cfEmail2" placeholder="Email Address *"
                                            value="{{ old('cfEmail2') }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-input">
                                        <input type="tel" name="cfPhone2" placeholder="Phone number"
                                            value="{{ old('cfPhone2') }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-input">
                                        <div class="tj-nice-select-box">
                                            <div class="tj-select">
                                                <select name="cfSubject2">
                                                    <option value="">Choose a service</option>
                                                    @foreach ($services as $service)
                                                        <option value="{{ $service->id }}"
                                                            @selected(old('cfSubject2') == $service->id)>
                                                            {{ $service->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-input message-input">
                                        <textarea name="cfMessage2" id="message" placeholder="Type message *" required>{{ old('cfMessage2') }}</textarea>
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
    <!-- start: Blog Section -->
    <!-- start: Blog Section -->
    <section class="tj-blog-section section-gap">
        <div class="container">
            {{-- Heading --}}
            <div class="row">
                <div class="col-12">
                    <div class="sec-heading text-center">
                        <span class="sub-title wow fadeInUp" data-wow-delay=".3s">
                            <i class="tji-box"></i>Events & Insights & Ideas
                        </span>
                        <h2 class="sec-title title-anim">The Ultimate <span>Resource.</span></h2>
                    </div>
                </div>
            </div>

            @php
                // Take up to 3 from each source
                $blogItems = ($blogs ?? collect())->take(3);
                $eventItems = ($events ?? collect())->take(3);
                $scheduleItems = ($upcomingSchedules ?? collect())->take(3);

                // Build one unified "event-card" style list
                $cards = collect();

                // BLOGS
                foreach ($blogItems as $blog) {
                    $cards->push([
                        'type' => 'blog',
                        'img' => $blog->thumbnail
                            ? asset('storage/' . $blog->thumbnail)
                            : asset('frontend/assets/images/blog/default.webp'),
                        'date' => optional($blog->created_at)->format('d'),
                        'month' => optional($blog->created_at)->format('M'),
                        'badge' => 'Blog',
                        'meta_right' => $blog->author->name ?? 'Admin',
                        'title' => $blog->title,
                        'url' => route('blogs.show', $blog->slug ?? $blog->id),
                        'cta' => 'Read More',
                    ]);
                }

                // EVENTS
                foreach ($eventItems as $event) {
                    $cards->push([
                        'type' => 'event',
                        'img' => $event->banner_image
                            ? asset('storage/' . $event->banner_image)
                            : asset('frontend/assets/images/project/project-6.webp'),
                        'date' => \Carbon\Carbon::parse($event->start_date)->format('d'),
                        'month' => \Carbon\Carbon::parse($event->start_date)->format('M'),
                        'badge' => 'Event',
                        'meta_right' => $event->location ?? 'Online',
                        'title' => $event->title,
                        'url' => route('events.show', $event->slug),
                        'cta' => 'View Details',
                    ]);
                }

            // $upcomingPerCourse should be from Option A in your controller
            // ->whereHas('schedules', fn($q) => $q->where('start_date','>=',now()))
            // ->with(['schedules' => fn($q) => $q->where('start_date','>=',now())->orderBy('start_date')->limit(1)])
            // ->withMin(['schedules as next_start' => fn($q) => $q->where('start_date','>=',now())], 'start_date')
            // ->orderBy('next_start','asc')->take(12)->get();

    $cards = collect();

    foreach ($upcomingSchedules as $course) {
        // nearest schedule per course (because you limited to 1 in the relation)
        $schedule = optional($course->schedules)->first();
        if (!$schedule) {
            continue; // safety: only courses that truly have an upcoming schedule
        }

        // safe thumbnail (only if file exists on public disk)
        $thumb = (!empty($course->thumbnail) && Storage::disk('public')->exists($course->thumbnail))
            ? asset('storage/' . $course->thumbnail)
            : asset('frontend/assets/images/service/service-1.webp');

        $title = $course->title ?? 'Bootcamp';
        $slug  = $course->slug ?? null;

        // Build card
        $cards->push([
            'type'       => 'schedule',
            'img'        => $thumb,
            'date'       => $schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('d') : '',
            'month'      => $schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('M') : '',
            'badge'      => ucfirst($schedule->type ?? 'virtual'),
            'meta_right' => $schedule->location ?: 'Online',
            // Prefer course details page; fall back to enroll link if needed
            'title'      => $title,
            'url'        => $slug ? route('shop.details', $slug) : route('enroll.pricing', $schedule->id),
            'cta'        => $slug ? 'View Details' : 'Enroll Now',
        ]);
    }
@endphp

<div class="row row-gap-4 mt-4">
    @if ($cards->isEmpty())
        {{-- EMPTY STATE --}}
        <div class="col-xl-4 col-md-6 mx-auto">
            <div class="blog-item wow fadeInUp" data-wow-delay=".4s" style="height:100%;">
                <div class="blog-thumb d-flex align-items-center justify-content-center"
                     style="height:250px;background:#fff;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.06);">
                    {{-- your SVG empty state --}}
                    <span class="text-muted">New bootcamps coming soon</span>
                </div>
                <div class="blog-content text-center">
                    <h4 class="title mb-1">Stay tuned</h4>
                    <p class="m-0">We’re lining up the next batch of schedules for you.</p>
                </div>
            </div>
        </div>
    @else
        {{-- unified row of event-style cards --}}
        @foreach ($cards as $i => $c)
            <div class="col-xl-4 col-md-6">
                <div class="blog-item wow fadeInUp" data-wow-delay=".4s">
                    <div class="blog-thumb">
                        <a href="{{ $c['url'] }}">
                            <img src="{{ $c['img'] }}" alt="{{ $c['title'] }}"
                                 style="height: 250px; width: 100%; object-fit: cover; border-radius: 8px;">
                        </a>
                        @if($c['date'] && $c['month'])
                            <div class="blog-date">
                                <span class="date">{{ $c['date'] }}</span>
                                <span class="month">{{ $c['month'] }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span class="categories"><a href="#">{{ $c['badge'] }}</a></span>
                            <span>{{ $c['meta_right'] }}</span>
                        </div>
                        <h4 class="title">
                            <a href="{{ $c['url'] }}">{{ $c['title'] }}</a>
                        </h4>
                        <a class="text-btn" href="{{ $c['url'] }}">
                            <span class="btn-text"><span>{{ $c['cta'] }}</span></span>
                            <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>


        </div>
    </section>

    <!-- end: Blog Section -->

    <!-- end: Blog Section -->



    <!-- end: Blog Section -->


@endsection
