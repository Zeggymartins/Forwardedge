@extends('user.master_page')
@section('title', ' Academy | Forward Edge Consulting')
@section('main')
@include('user.partials.breadcrumb')
     <!-- start: Service Section -->
<section class="tj-service-section service-4 section-gap">
    <div class="container">
        <div class="row row-gap-4">
            @foreach ($course as $index => $course)
                <div class="col-lg-4 col-md-6">
                    <div class="service-item style-4 wow fadeInUp" data-wow-delay=".{{ $index + 1 }}s">
                        <div class="service-icon">
                            {{-- You can map icons manually or store them in DB --}}
                            <i class="tji-service-{{ ($index % 6) + 1 }}"></i>
                        </div>
                        <div class="service-content">
                            <h4 class="title">
                                <a href="">
                                    {{ $course->title }}
                                </a>
                            </h4>
                            <p class="desc">{{ $course->description }}</p>
                            <a class="text-btn" href="{{ route('course.show', $course->slug) }}">
                                <span class="btn-text"><span>Go to Course</span></span>
                                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>


        <!-- end: Service Section -->

        <!-- start: Contact Section -->
        <section class="tj-contact-section section-gap section-gap-x">
          <div class="container">
            <div class="row">
              <div class="col-lg-6">
                <div class="global-map wow fadeInUp" data-wow-delay=".3s">
                  <div class="global-map-img">
                    <img src="{{asset('frontend/assets/images/bg/map.svg')}}" alt="Image">
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
                    <span class="sub-title text-white"><i class="tji-box"></i>Get in Touch</span>
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
                                <option value="4">Training and Development</option>
                                <option value="5">IT Support & Maintenance</option>
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
            <img src="{{asset('frontend/assets/images/shape/pattern-2.svg')}}" alt="">
          </div>
          <div class="bg-shape-2">
            <img src="{{asset('frontend/assets/images/shape/pattern-3.svg')}}" alt="">
          </div>
        </section>
        <!-- end: Contact Section -->



        <!-- start: Client Section -->
        <section class="tj-client-section client-section-gap-2 wow fadeInUp" data-wow-delay=".4s">
          <div class="container-fluid client-container">
            <div class="row">
              <div class="col-12">
                <div class="client-content">
                  <h5 class="sec-title">Join Over <span class="client-numbers">1000+</span> Companies with
                    <span class="client-text">Bexon</span> Here
                  </h5>
                </div>
                <div class="swiper client-slider client-slider-1">
                  <div class="swiper-wrapper">
                    <div class="swiper-slide client-item">
                      <div class="client-logo">
                        <img src="{{asset('frontend/assets/images/brands/brand-1.webp')}}" alt="">
                      </div>
                    </div>
                    <div class="swiper-slide client-item">
                      <div class="client-logo">
                        <img src="{{asset('frontend/assets/images/brands/brand-2.webp')}}" alt="">
                      </div>
                    </div>
                    <div class="swiper-slide client-item">
                      <div class="client-logo">
                        <img src="{{asset('frontend/assets/images/brands/brand-3.webp')}}" alt="">
                      </div>
                    </div>
                    <div class="swiper-slide client-item">
                      <div class="client-logo">
                        <img src="{{asset('frontend/assets/images/brands/brand-4.webp')}}" alt="">
                      </div>
                    </div>
                    <div class="swiper-slide client-item">
                      <div class="client-logo">
                        <img src="{{asset('frontend/assets/images/brands/brand-5.webp')}}" alt="">
                      </div>
                    </div>
                    <div class="swiper-slide client-item">
                      <div class="client-logo">
                        <img src="{{asset('frontend/assets/images/brands/brand-6.webp')}}" alt="">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- end: Client Section -->

    
@endsection