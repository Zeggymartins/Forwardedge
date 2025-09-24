@extends('user.master_page')
@section('title', ($course->name ?? 'Course Details') . ' | Forward Edge Consulting')
@push('styles')
    <style>
        .image-box {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .image-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .image-box img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
            transition: filter 0.3s ease-in-out;
        }

        .image-box .enroll-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 12px 24px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            border-radius: 25px;
            opacity: 0;
            transition: all 0.3s ease-in-out;
            border: none;
            cursor: pointer;
        }

        .image-box:hover .enroll-btn {
            opacity: 1;
        }

        .image-box:hover img {
            filter: brightness(70%);
        }

        .phase-card {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .phase-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .phase-number {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .topic-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .topic-list li:last-child {
            border-bottom: none;
        }

        .schedule-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }

        .price-tag {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            display: inline-block;
        }

        .course-stats {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
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
                        {{-- Course Hero Image --}}
                        <div class="blog-images wow fadeInUp" data-wow-delay=".1s">
                            <img src="{{ ($course->thumbnail && file_exists(storage_path('app/public/' . $course->thumbnail))) 
                                        ? asset('storage/' . $course->thumbnail) 
                                        : asset('frontend/assets/images/service/service-1.webp') }}"
                                alt="{{ $course->title ?? 'Course' }}"
                                class="img-fluid"
                                style="height: 400px; width: 100%; object-fit: cover; border-radius: 12px;">
                        </div>

                        {{-- Course Title and Basic Info --}}
                        <div class="course-header mt-4">
                            <h2 class="title title-anim">{{ $course->title ?? 'Digital Marketing Mastery Program' }}</h2>
                            
                            <div class="course-meta d-flex flex-wrap align-items-center gap-4 mt-3">
                                @if($course->schedules && $course->schedules->count() > 0)
                                    <div class="meta-item">
                                        <span class="price-tag">
                                            ₦{{ number_format($course->schedules->first()->price ?? 250000, 0) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="meta-item">
                                    <i class="tji-clock me-2"></i>
                                    <span>{{ $course->duration ?? '12 Weeks' }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="tji-user me-2"></i>
                                    <span>{{ $course->level ?? 'All Levels' }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="tji-calendar me-2"></i>
                                    <span>Next Batch: {{ $course->schedules->first()->start_date ?? 'Coming Soon' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Course Overview --}}
                        <div class="blog-text mt-4">
                            <p class="wow fadeInUp" data-wow-delay=".3s" style="font-size: 1.1rem; line-height: 1.7;">
                                {{ $course->description ?? 'Unlock the power of digital marketing with our comprehensive program designed for professionals who want to master the latest strategies, tools, and techniques in today\'s digital landscape. This intensive course combines theoretical knowledge with hands-on practical experience to ensure you\'re ready to drive real results for any organization.' }}
                            </p>

                            {{-- Course Stats --}}
                            <div class="course-stats wow fadeInUp" data-wow-delay=".4s">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <div class="stat-number">{{ $course->phases->count() ?: '4' }}</div>
                                            <div class="stat-label">Learning Phases</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <div class="stat-number">{{ $course->phases->sum(function($phase) { return $phase->topics->count(); }) ?: '24' }}</div>
                                            <div class="stat-label">Topics Covered</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <div class="stat-number">95%</div>
                                            <div class="stat-label">Success Rate</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="stat-item">
                                            <div class="stat-number">500+</div>
                                            <div class="stat-label">Graduates</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Course Phases --}}
                            <h3 class="wow fadeInUp" data-wow-delay=".5s">Course Curriculum</h3>
                            <p class="wow fadeInUp" data-wow-delay=".6s">
                                Our structured learning path takes you from fundamentals to advanced implementation, 
                                ensuring you build solid expertise at each stage.
                            </p>

                            <div class="row row-gap-4 mt-4">
                                @if($course->phases && $course->phases->count() > 0)
                                    @foreach ($course->phases->sortBy('order') as $phase)
                                        <div class="col-xl-6 col-md-6">
                                            <div class="phase-card wow fadeInUp" data-wow-delay=".{{ 5 + $loop->iteration * 2 }}s">
                                                <div class="phase-number">{{ $phase->order }}</div>
                                                <h5 class="phase-title mb-3">{{ $phase->title }}</h5>
                                                <p class="phase-description mb-3">{{ $phase->description ?? 'Comprehensive coverage of essential concepts and practical applications.' }}</p>
                                                
                                                @if($phase->topics && $phase->topics->count() > 0)
                                                    <ul class="topic-list list-unstyled">
                                                        @foreach($phase->topics->take(4) as $topic)
                                                            <li>
                                                                <i class="tji-check text-success me-2"></i>
                                                                {{ $topic->title }}
                                                            </li>
                                                        @endforeach
                                                        @if($phase->topics->count() > 4)
                                                            <li class="text-muted">
                                                                <i class="tji-plus me-2"></i>
                                                                {{ $phase->topics->count() - 4 }} more topics...
                                                            </li>
                                                        @endif
                                                    </ul>
                                                @else
                                                    <ul class="topic-list list-unstyled">
                                                        <li><i class="tji-check text-success me-2"></i>Core concepts and fundamentals</li>
                                                        <li><i class="tji-check text-success me-2"></i>Practical exercises and projects</li>
                                                        <li><i class="tji-check text-success me-2"></i>Real-world case studies</li>
                                                        <li><i class="tji-check text-success me-2"></i>Assessment and feedback</li>
                                                    </ul>
                                                @endif

                                                <div class="phase-duration mt-3">
                                                    <small class="text-muted">
                                                        <i class="tji-clock me-1"></i>
                                                        Duration: {{ $phase->duration ?? '3 weeks' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- Dummy Phases --}}
                                    @for($i = 1; $i <= 4; $i++)
                                        <div class="col-xl-6 col-md-6">
                                            <div class="phase-card wow fadeInUp" data-wow-delay=".{{ 5 + $i * 2 }}s">
                                                <div class="phase-number">{{ $i }}</div>
                                                <h5 class="phase-title mb-3">
                                                    @switch($i)
                                                        @case(1) Foundation & Strategy @break
                                                        @case(2) Content & Social Media @break  
                                                        @case(3) Paid Advertising & Analytics @break
                                                        @case(4) Advanced Tactics & Optimization @break
                                                    @endswitch
                                                </h5>
                                                <p class="phase-description mb-3">
                                                    @switch($i)
                                                        @case(1) Build your foundation with digital marketing fundamentals, strategy development, and market analysis. @break
                                                        @case(2) Master content creation, social media marketing, and community engagement strategies. @break
                                                        @case(3) Learn paid advertising platforms, conversion tracking, and data-driven decision making. @break
                                                        @case(4) Implement advanced techniques, automation, and continuous optimization strategies. @break
                                                    @endswitch
                                                </p>
                                                
                                                <ul class="topic-list list-unstyled">
                                                    @switch($i)
                                                        @case(1)
                                                            <li><i class="tji-check text-success me-2"></i>Digital Marketing Landscape</li>
                                                            <li><i class="tji-check text-success me-2"></i>Target Audience Research</li>
                                                            <li><i class="tji-check text-success me-2"></i>Competitive Analysis</li>
                                                            <li><i class="tji-check text-success me-2"></i>Strategic Planning</li>
                                                            @break
                                                        @case(2)
                                                            <li><i class="tji-check text-success me-2"></i>Content Marketing Strategy</li>
                                                            <li><i class="tji-check text-success me-2"></i>Social Media Platforms</li>
                                                            <li><i class="tji-check text-success me-2"></i>Community Building</li>
                                                            <li><i class="tji-check text-success me-2"></i>Influencer Partnerships</li>
                                                            @break
                                                        @case(3)
                                                            <li><i class="tji-check text-success me-2"></i>Google Ads Mastery</li>
                                                            <li><i class="tji-check text-success me-2"></i>Facebook & Instagram Ads</li>
                                                            <li><i class="tji-check text-success me-2"></i>Analytics & Tracking</li>
                                                            <li><i class="tji-check text-success me-2"></i>ROI Optimization</li>
                                                            @break
                                                        @case(4)
                                                            <li><i class="tji-check text-success me-2"></i>Marketing Automation</li>
                                                            <li><i class="tji-check text-success me-2"></i>Advanced SEO Techniques</li>
                                                            <li><i class="tji-check text-success me-2"></i>Conversion Rate Optimization</li>
                                                            <li><i class="tji-check text-success me-2"></i>Growth Hacking Strategies</li>
                                                            @break
                                                    @endswitch
                                                </ul>

                                                <div class="phase-duration mt-3">
                                                    <small class="text-muted">
                                                        <i class="tji-clock me-1"></i>
                                                        Duration: 3 weeks
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                @endif
                            </div>

                            {{-- Schedule Information --}}
                            @if($course->schedules && $course->schedules->count() > 0)
                                <h3 class="wow fadeInUp mt-5" data-wow-delay=".3s">Upcoming Schedule</h3>
                                @foreach($course->schedules->take(3) as $schedule)
                                    <div class="schedule-card wow fadeInUp" data-wow-delay=".{{ 4 + $loop->iteration }}s">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <h5 class="mb-2">Batch {{ $loop->iteration }}</h5>
                                                <p class="mb-1">
                                                    <i class="tji-calendar me-2"></i>
                                                    <strong>Start Date:</strong> {{ \Carbon\Carbon::parse($schedule->start_date)->format('M j, Y') }}
                                                </p>
                                                <p class="mb-1">
                                                    <i class="tji-calendar me-2"></i>
                                                    <strong>End Date:</strong> {{ \Carbon\Carbon::parse($schedule->end_date)->format('M j, Y') }}
                                                </p>
                                                <p class="mb-0">
                                                    <i class="tji-location me-2"></i>
                                                    <strong>Format:</strong> {{ ucfirst($schedule->type) ?? 'Online' }}
                                                </p>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <div class="price-tag mb-3">
                                                    ₦{{ number_format($schedule->price, 0) }}
                                                </div>
                                                <button class="tj-primary-btn enroll-btn"  data-schedule-id="{{ $schedule->id }}"
        data-enroll-url="{{ route('enroll.pricing', $schedule->id) }}>
                                                    <span class="btn-text">Enroll Now</span>
                                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="schedule-card wow fadeInUp" data-wow-delay=".4s">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5 class="mb-2">Next Batch</h5>
                                            <p class="mb-1">
                                                <i class="tji-calendar me-2"></i>
                                                <strong>Start Date:</strong> {{ now()->addWeeks(2)->format('M j, Y') }}
                                            </p>
                                            <p class="mb-1">
                                                <i class="tji-calendar me-2"></i>
                                                <strong>End Date:</strong> {{ now()->addWeeks(14)->format('M j, Y') }}
                                            </p>
                                            <p class="mb-0">
                                                <i class="tji-location me-2"></i>
                                                <strong>Format:</strong> Hybrid (Online + In-person)
                                            </p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <div class="price-tag mb-3">₦250,000</div>
                                            <button class="tj-primary-btn enroll-btn" data-schedule-id="1">
                                                <span class="btn-text">Enroll Now</span>
                                                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- What You'll Learn --}}
                            <h3 class="wow fadeInUp mt-5" data-wow-delay=".3s">What You'll Learn</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="wow fadeInUp" data-wow-delay=".4s">
                                        <li><span><i class="tji-check"></i></span>Complete digital marketing strategy development</li>
                                        <li><span><i class="tji-check"></i></span>Advanced social media marketing techniques</li>
                                        <li><span><i class="tji-check"></i></span>Pay-per-click advertising mastery</li>
                                        <li><span><i class="tji-check"></i></span>Search engine optimization (SEO) best practices</li>
                                        <li><span><i class="tji-check"></i></span>Content marketing and storytelling</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="wow fadeInUp" data-wow-delay=".5s">
                                        <li><span><i class="tji-check"></i></span>Email marketing automation</li>
                                        <li><span><i class="tji-check"></i></span>Analytics and performance tracking</li>
                                        <li><span><i class="tji-check"></i></span>Conversion rate optimization</li>
                                        <li><span><i class="tji-check"></i></span>Marketing tools and platforms</li>
                                        <li><span><i class="tji-check"></i></span>ROI measurement and reporting</li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Course Highlights --}}
                            <div class="images-wrap mt-5">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="image-box wow fadeInUp" data-wow-delay=".6s">
                                            <img src="{{ asset('frontend/assets/images/service/service-3.webp') }}" alt="Hands-on Learning">
                                            <button class="enroll-btn" data-schedule-id="{{ $course->schedules->first()->id ?? '1' }}">
                                                Start Learning
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="image-box wow fadeInUp" data-wow-delay=".7s">
                                            <img src="{{ asset('frontend/assets/images/service/service-4.webp') }}" alt="Expert Instruction">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Course Benefits --}}
                            <h3 class="wow fadeInUp mt-5" data-wow-delay=".3s">Why Choose This Course?</h3>
                            <p class="wow fadeInUp" data-wow-delay=".4s">
                                Our comprehensive approach combines theoretical knowledge with practical application, 
                                ensuring you're ready to implement what you learn immediately in your professional environment.
                            </p>

                            <div class="details-content-box">
                                <div class="row row-gap-4">
                                    <div class="col-xl-4 col-md-6">
                                        <div class="service-details-item wow fadeInUp" data-wow-delay=".5s">
                                            <span class="number">01.</span>
                                            <h6 class="title">Expert-Led Training</h6>
                                            <div class="desc">
                                                <p>Learn from industry professionals with over 10 years of digital marketing experience and proven track records.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-6">
                                        <div class="service-details-item wow fadeInUp" data-wow-delay=".7s">
                                            <span class="number">02.</span>
                                            <h6 class="title">Hands-On Projects</h6>
                                            <div class="desc">
                                                <p>Work on real client projects and build a portfolio that demonstrates your skills to potential employers.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-6">
                                        <div class="service-details-item wow fadeInUp" data-wow-delay=".9s">
                                            <span class="number">03.</span>
                                            <h6 class="title">Certification & Support</h6>
                                            <div class="desc">
                                                <p>Receive industry-recognized certification and ongoing career support to help you advance professionally.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Performance Stats --}}
                            <div class="countup-wrap">
                                <div class="countup-item">
                                    <div class="inline-content">
                                        <span class="odometer countup-number" data-count="95"></span>
                                        <span class="count-plus">%</span>
                                    </div>
                                    <span class="count-text">Job Placement Rate</span>
                                    <span class="count-separator" data-bg-image="{{ asset('frontend/assets/images/shape/separator.svg') }}"></span>
                                </div>
                                <div class="countup-item">
                                    <div class="inline-content">
                                        <span class="odometer countup-number" data-count="500"></span>
                                        <span class="count-plus">+</span>
                                    </div>
                                    <span class="count-text">Successful Graduates</span>
                                    <span class="count-separator" data-bg-image="{{ asset('frontend/assets/images/shape/separator.svg') }}"></span>
                                </div>
                                <div class="countup-item">
                                    <div class="inline-content">
                                        <span class="odometer countup-number" data-count="4.8"></span>
                                        <span class="count-plus">/5</span>
                                    </div>
                                    <span class="count-text">Student Rating</span>
                                    <span class="count-separator" data-bg-image="{{ asset('frontend/assets/images/shape/separator.svg') }}"></span>
                                </div>
                                <div class="countup-item">
                                    <div class="inline-content">
                                        <span class="odometer countup-number" data-count="24</span>
                                        <span class="count-plus">/7</span>
                                    </div>
                                    <span class="count-text">Learning Support</span>
                                </div>
                            </div>

                            {{-- FAQ Section --}}
                            <h3 class="wow fadeInUp mt-5" data-wow-delay=".3s">Frequently Asked Questions</h3>
                            <div class="accordion tj-faq style-2" id="faqOne">
                                <div class="accordion-item active wow fadeInUp" data-wow-delay=".4s">
                                    <button class="faq-title" type="button" data-bs-toggle="collapse" data-bs-target="#faq-1" aria-expanded="true">
                                        What are the prerequisites for this course?
                                    </button>
                                    <div id="faq-1" class="collapse show" data-bs-parent="#faqOne">
                                        <div class="accordion-body faq-text">
                                            <p>No prior digital marketing experience is required. Basic computer literacy and internet familiarity are helpful but not mandatory. We start from fundamentals and build up to advanced concepts.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item wow fadeInUp" data-wow-delay=".5s">
                                    <button class="faq-title collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-2" aria-expanded="false">
                                        What support is available during the course?
                                    </button>
                                    <div id="faq-2" class="collapse" data-bs-parent="#faqOne">
                                        <div class="accordion-body faq-text">
                                            <p>You'll have access to dedicated instructors, peer collaboration forums, weekly office hours, and our comprehensive learning management system. Plus, career counseling and job placement assistance.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item wow fadeInUp" data-wow-delay=".6s">
                                    <button class="faq-title collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-3" aria-expanded="false">
                                        Are there flexible payment options available?
                                    </button>
                                    <div id="faq-3" class="collapse" data-bs-parent="#faqOne">
                                        <div class="accordion-body faq-text">
                                            <p>Yes! We offer both full payment and installment plans. You can pay 70% upfront and the remaining 30% during the course. Scholarships and corporate training discounts are also available.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item wow fadeInUp" data-wow-delay=".7s">
                                    <button class="faq-title collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-4" aria-expanded="false">
                                        What certification will I receive upon completion?
                                    </button>
                                    <div id="faq-4" class="collapse" data-bs-parent="#faqOne">
                                        <div class="accordion-body faq-text">
                                            <p>You'll receive a Forward Edge Consulting Digital Marketing Professional Certificate, plus preparation for industry certifications like Google Ads, Facebook Blueprint, and HubSpot Content Marketing.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Navigation --}}
                        <div class="tj-post__navigation mb-0 wow fadeInUp" data-wow-delay=".3s">
                            <div class="tj-nav__post previous">
                                <div class="tj-nav-post__nav prev_post">
                                    <a href="{{ route('academy') }}"><span><i class="tji-arrow-left"></i></span>All Courses</a>
                                </div>
                            </div>
                            <div class="tj-nav-post__grid">
                                <a href="{{ route('academy') }}"><i class="tji-window"></i></a>
                            </div>
                            <div class="tj-nav__post next">
                                <div class="tj-nav-post__nav next_post">
                                    <a href="{{ route('shop') }}">Browse Shop<span><i class="tji-arrow-right"></i></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

