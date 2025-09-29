@extends('user.master_page')
@section('title', 'Service Details | Forward Edge Consulting')
@section('main')
    @include('user.partials.breadcrumb')
    <section class="tj-blog-section section-gap slidebar-stickiy-container">
        <div class="container">
            <div class="row row-gap-5">
                <div class="col-lg-8">
                    <div class="post-details-wrapper">

                        {{-- Thumbnail --}}
                        <div class="blog-images wow fadeInUp" data-wow-delay=".1s">
                            <img src="{{ $service->thumbnail ? asset('storage/' . $service->thumbnail) : asset('frontend/assets/images/service/service-details.webp') }}"
                                alt="{{ $service->title }}" class="img-fluid">
                        </div>

                        {{-- Title --}}
                        <h2 class="title title-anim">{{ $service->title }}</h2>

                        <div class="blog-text">
                            {{-- Loop through dynamic content --}}
                            @foreach ($service->contents as $content)
                                @if ($content->type === 'heading')
                                    <h3 class="wow fadeInUp" data-wow-delay=".3s">
                                        {!! $content->content !!}
                                    </h3>
                                @elseif($content->type === 'paragraph')
                                    <p class="wow fadeInUp" data-wow-delay=".3s">
                                        {!! $content->content !!}
                                    </p>
                                    {{-- LIST --}}
                                @elseif($content->type === 'list')
                                    @php
                                        $items = $content->content;
                                        if (is_string($items)) {
                                            $decoded = json_decode($items, true);
                                            if (json_last_error() === JSON_ERROR_NONE) {
                                                $items = $decoded;
                                            } else {
                                                $items = [$items];
                                            }
                                        }
                                    @endphp
                                    <ul class="wow fadeInUp" data-wow-delay=".3s">
                                        @foreach ($items as $item)
                                            <li><span><i class="tji-check"></i></span>{{ $item }}</li>
                                        @endforeach
                                    </ul>

                                    {{-- IMAGE --}}
                                @elseif($content->type === 'image')
                                    @php
                                        $images = $content->content;
                                        if (is_string($images)) {
                                            $decoded = json_decode($images, true);
                                            $images = json_last_error() === JSON_ERROR_NONE ? $decoded : [$images];
                                        }
                                        if (!is_array($images)) {
                                            $images = [$images];
                                        }
                                        $images = array_pad($images, 2, null);
                                    @endphp
                                    <div class="images-wrap">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="image-box wow fadeInUp" data-wow-delay=".3s">
                                                    <img src="{{ $images[0] ? asset('storage/' . $images[0]) : asset('frontend/assets/images/service/service-3.webp') }}"
                                                        alt="Service Image">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="image-box wow fadeInUp" data-wow-delay=".5s">
                                                    <img src="{{ $images[1] ? asset('storage/' . $images[1]) : asset('frontend/assets/images/service/service-4.webp') }}"
                                                        alt="Service Image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- FEATURE --}}
                                @elseif($content->type === 'feature')
                                    @php
                                        $feature = $content->content;
                                        if (is_string($feature)) {
                                            $decoded = json_decode($feature, true);
                                            $feature = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
                                        }
                                    @endphp
                                    @if ($loop->first || $service->contents[$loop->index - 1]->type !== 'feature')
                                        <div class="details-content-box">
                                    @endif

                                    <div class="service-details-item wow fadeInUp"
                                        data-wow-delay=".{{ (($loop->index % 3) + 1) * 2 }}s">
                                        <span
                                            class="number">{{ $feature['number'] ?? sprintf('%02d.', $loop->iteration) }}</span>
                                        <h6 class="title">{{ $feature['title'] ?? '' }}</h6>
                                        <div class="desc">
                                            <p>{{ $feature['description'] ?? '' }}</p>
                                        </div>
                                    </div>

                                    @if ($loop->last || $service->contents[$loop->index + 1]->type !== 'feature')
                        </div>
                        @endif
                        @endif
                        @endforeach

                        {{-- Static FAQ Section (add this if you want FAQs) --}}
                        <h3 class="wow fadeInUp" data-wow-delay=".3s">Frequently asked questions</h3>
                        <div class="accordion tj-faq style-2" id="faqOne">
                            <div class="accordion-item active wow fadeInUp" data-wow-delay=".3s">
                                <button class="faq-title" type="button" data-bs-toggle="collapse" data-bs-target="#faq-1"
                                    aria-expanded="true">
                                    What is Customer Experience (CX) and why is it important?
                                </button>
                                <div id="faq-1" class="collapse show" data-bs-parent="#faqOne">
                                    <div class="accordion-body faq-text">
                                        <p>Customer Experience (CX) refers to the overall impression a customer has of a
                                            business based on their interactions across various touchpoints—whether it's a
                                            website visit, a customer support call, or an in-store purchase.</p>
                                    </div>
                                </div>
                            </div>
                            {{-- Add more FAQ items as needed --}}
                        </div>
                    </div>

                    {{-- Navigation --}}
                    <div class="tj-post__navigation mb-0 wow fadeInUp" data-wow-delay=".3s">
                        <!-- previous post -->
                        <div class="tj-nav__post previous">
                            <div class="tj-nav-post__nav prev_post">
                                <a href="#"><span><i class="tji-arrow-left"></i></span>Previous</a>
                            </div>
                        </div>
                        <div class="tj-nav-post__grid">
                            <a href=""><i class="tji-window"></i></a>
                        </div>
                        <!-- next post -->
                        <div class="tj-nav__post next">
                            <div class="tj-nav-post__nav next_post">
                                <a href="#">Next<span><i class="tji-arrow-right"></i></span></a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-4">
                <div class="tj-main-sidebar slidebar-stickiy">
                    <div class="tj-sidebar-widget service-categories wow fadeInUp" data-wow-delay=".1s">
                        <h4 class="widget-title">More services</h4>
                        <ul>
                            @foreach ($otherServices as $s)
                                <li>
                                    <a href="{{ route('services.show', $s->slug) }}"
                                        class="{{ $s->id == $service->id ? 'active' : '' }}">
                                        {{ $s->title }}
                                        <span class="icon"><i class="tji-arrow-right"></i></span>
                                    </a>
                                </li>
                            @endforeach

                        </ul>
                    </div>

                    {{-- Feature Widget --}}
                    <div class="tj-sidebar-widget widget-feature-item wow fadeInUp" data-wow-delay=".3s">
                        <div class="feature-box">
                            <div class="feature-content">
                                <h2 class="title">Modern</h2>
                                <span>Home Makeover</span>
                                <a class="read-more feature-contact" href="tel:8321890640">
                                    <i class="tji-phone-3"></i>
                                    <span>+8 (321) 890-640</span>
                                </a>
                            </div>
                            <div class="feature-images">
                                <img src="{{ asset('frontend/assets/images/service/service-ad.webp') }}" alt="Feature">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        </div>
    </section>
@endsection
