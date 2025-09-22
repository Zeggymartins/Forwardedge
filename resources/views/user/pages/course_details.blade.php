@extends('user.master_page')
@section('title', ' Course Details| Forward Edge Consulting')
@push('styles')
    <style>
        .image-box {
            position: relative;
            overflow: hidden;
        }

        .image-box img {
            width: 100%;
            display: block;
            transition: filter 0.3s ease-in-out;
        }

        /* Enroll button styling */
        .image-box .enroll-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 10px 20px;
            background: rgba(0, 123, 255, 0.9);
            color: #fff;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        /* Show button on hover */
        .image-box:hover .enroll-btn {
            opacity: 1;
        }

        /* Optional: darken image on hover */
        .image-box:hover img {
            filter: brightness(70%);
        }
    </style>
@endpush
@section('main')
  @include('user.partials.breadcrumb')
    <section class="tj-blog-section section-gap slidebar-stickiy-container">
        <div class="container">
            <div class="row row-gap-5">
                <div class="col-lg-12">
                    <div class="post-details-wrapper">
                        <div class="blog-images wow fadeInUp" data-wow-delay=".1s">
                           <img
                              src="{{ $course->thumbnail && file_exists(storage_path('app/public/' . $course->thumbnail))
                                    ? asset('storage/' . $course->thumbnail)
                                    : asset('frontend/assets/images/service/service-3.webp') }}"
                              alt="{{ $course->title ?? 'Course image' }}"
                              loading="lazy">
                        </div>

                        <h2 class="title title-anim">{{ $course->title }}</h2>

                        <div class="blog-text">
                            <p class="wow fadeInUp" data-wow-delay=".3s">
                                {{ $course->description }}
                            </p>

                            <div class="row row-gap-4">
                                @foreach ($course->phases->sortBy('order') as $phase)
                                    <div class="col-xl-4 col-md-6">
                                        <div class="project-item wow fadeInUp" data-wow-delay=".{{ $loop->iteration }}s">
                                            <div class="project-img">
                                                 <img
                                                    src="{{ ($phase->image && file_exists(storage_path('app/public/' . $phase->image)))
                                                          ? asset('storage/' . $phase->image)
                                                          : asset('frontend/assets/images/service/service-4.webp') }}"
                                                    alt="{{ $phase->title ?? 'Phase image' }}"
                                                    loading="lazy">
                                            </div>
                                            <div class="project-content">
                                                <span class="categories"><a href="#">Phase
                                                        {{ $phase->order }}</a></span>
                                                <div class="project-text">
                                                    <h4 class="title">
                                                        <a href="#">
                                                            {{ $phase->title }}
                                                        </a>
                                                    </h4>
                                                    <a class="project-btn" href="#">
                                                        <i class="tji-arrow-right-big"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <p class="wow fadeInUp mt-4" data-wow-delay=".3s">Our approach to digital skill learning is structured and data-driven. We begin by evaluating your current level, highlighting key areas for improvement, and applying proven strategies to strengthen your abilities. From building strong foundations to mastering advanced digital tools, this course is designed to support continuous growth and practical application.</p>

                        <div class="images-wrap">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="image-box wow fadeInUp" data-wow-delay=".3s">
                                        <img src="{{ asset('frontend/assets/images/service/service-3.webp') }}"
                                            alt="Image">
                                      <a href="javascript:void(0)" class="btn btn-success enroll-btn" 
                                                 data-schedule-id="{{ $course->schedules->first()->id ?? '' }}">
                                                Enroll
                                      </a>

                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="image-box wow fadeInUp" data-wow-delay=".5s">
                                        <img src="{{ asset('frontend/assets/images/service/service-4.webp') }}"
                                            alt="Image">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3 class="wow fadeInUp" data-wow-delay=".3s">Our Range of Customer Services</h3>
                        <p class="wow fadeInUp" data-wow-delay=".3s">At Bexon, we don't just focus on solving customer
                            problems—we focus on creating experiences that
                            delight and build lasting relationships. Whether it's through improving customer service
                            operations,
                            leveraging technology, or designing more engaging digital experiences, our team is here to
                            help
                            you
                            exceed your customers' expectations every time. We help you understand your customers
                            deeply,
                            optimize
                            their experience.</p>
                        <div class="details-content-box">
                            <div class="row row-gap-4">
                                @foreach ($course->phases->sortBy('order') as $phase)
                                    <div class="col-xl-4 col-md-6">
                                        <div class="service-details-item wow fadeInUp"
                                            data-wow-delay=".{{ $loop->iteration * 2 }}s">

                                            <!-- Phase Header -->
                                            <h5 class="title text-center">
                                                Phase <span
                                                    class="number m-2">{{ str_pad($phase->order, 2, '0', STR_PAD_LEFT) }}.</span>
                                            </h5>
                                            <h6 class="title text-center">{{ $phase->title }}</h6>

                                            <!-- Description -->
                                            <div class="desc">
                                                <p>{{ $phase->description }}</p>

                                                <!-- Topics List -->
                                                @if ($phase->topics->count())
                                                    <ul class="wow fadeInUp" data-wow-delay=".3s">
                                                        @foreach ($phase->topics as $topic)
                                                            <li>
                                                                <span><i class="tji-check"></i></span>
                                                                {{ $topic->title }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-muted"><em>No topics available yet.</em></p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
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
                        <h3 class="wow fadeInUp mt-5" data-wow-delay=".3s">Frequently asked questions</h3>
                        <div class="accordion tj-faq style-2" id="faqOne">
                            <div class="accordion-item active wow fadeInUp" data-wow-delay=".3s">
                                <button class=" faq-title" type="button" data-bs-toggle="collapse" data-bs-target="#faq-1"
                                    aria-expanded="true">What is Customer Experience (CX)
                                    and why is it important?</button>
                                <div id="faq-1" class="collapse show" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Customer Experience (CX) refers to the overall impression a customer has
                                            of a business
                                            based
                                            on their interactions across various touchpoints—whether it’s a website
                                            visit, a customer
                                            support call, or an in-store purchase. It encompasses everything from
                                            ease of navigation
                                            and
                                            service quality to emotional connection and brand perception.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item wow fadeInUp" data-wow-delay=".3s">
                                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq-2" aria-expanded="false">How can your Customer Experience
                                    Solutions
                                    benefit?</button>
                                <div id="faq-2" class="collapse" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Our solutions optimize every touchpoint of the customer journey, ensuring
                                            seamless,
                                            personalized, and meaningful interactions. The benefits include improved
                                            customer
                                            satisfaction, higher retention rates, stronger brand loyalty, and
                                            actionable insights to
                                            continuously improve your customer engagement strategies. We help
                                            integrate these channels
                                            so
                                            customers feel valued.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item wow fadeInUp" data-wow-delay=".3s">
                                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq-3" aria-expanded="false">How do you personalize the
                                    customer
                                    experience?</button>
                                <div id="faq-3" class="collapse" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Getting started is easy! Simply reach out to us through our contact form
                                            or give us a
                                            call,
                                            and
                                            we’ll schedule a consultation to discuss your project and how we can
                                            best assist you. Our
                                            team
                                            keeps you informed throughout the process, ensuring quality control and
                                            timely delivery.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item wow fadeInUp" data-wow-delay=".3s">
                                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq-4" aria-expanded="false">What kind of tools do you use to
                                    improve
                                    customer experience?</button>
                                <div id="faq-4" class="collapse" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Getting started is easy! Simply reach out to us through our contact form
                                            or give us a
                                            call,
                                            and
                                            we’ll schedule a consultation to discuss your project and how we can
                                            best assist you. Our
                                            team
                                            keeps you informed throughout the process, ensuring quality control and
                                            timely delivery.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item wow fadeInUp" data-wow-delay=".3s">
                                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq-5" aria-expanded="false">How do you collect customer
                                    feedback?</button>
                                <div id="faq-5" class="collapse" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Getting started is easy! Simply reach out to us through our contact form
                                            or give us a
                                            call,
                                            and
                                            we’ll schedule a consultation to discuss your project and how we can
                                            best assist you. Our
                                            team
                                            keeps you informed throughout the process, ensuring quality control and
                                            timely delivery.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item wow fadeInUp" data-wow-delay=".3s">
                                <button class="faq-title collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faq-6" aria-expanded="false">Can you help improve our
                                    customer support
                                    system?</button>
                                <div id="faq-6" class="collapse" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Getting started is easy! Simply reach out to us through our contact form
                                            or give us a
                                            call,
                                            and
                                            we’ll schedule a consultation to discuss your project and how we can
                                            best assist you. Our
                                            team
                                            keeps you informed throughout the process, ensuring quality control and
                                            timely delivery.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tj-post__navigation mb-0 wow fadeInUp" data-wow-delay=".3s">
                        <!-- previous post -->
                        <div class="tj-nav__post previous">
                            <div class="tj-nav-post__nav prev_post">
                                <a href="service-details.html"><span><i class="tji-arrow-left"></i></span>Previous</a>
                            </div>
                        </div>
                        <div class="tj-nav-post__grid">
                            <a href="service.html"><i class="tji-window"></i></a>
                        </div>
                        <!-- next post -->
                        <div class="tj-nav__post next">
                            <div class="tj-nav-post__nav next_post">
                                <a href="service-details.html">Next<span><i class="tji-arrow-right"></i></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
    </section>
@endsection
