@extends('user.master_page')
@section('title', ' About Us | Forward Edge Consulting')
@section('main')
@include('user.partials.breadcrumb')
 <!-- start: Choose Section -->
        <section id="choose" class="tj-choose-section section-gap">
          <div class="container">
            <div class="row">
              <div class="col-12">
                <div class="sec-heading-wrap">
                  <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>What we do</span>
                  <div class="heading-wrap-content">
                    <div class="sec-heading">
                      <h2 class="sec-title title-anim">Empowering Business with <span>Expertise.</span></h2>
                    </div>
                    <!-- <div class="btn-wrap wow fadeInUp" data-wow-delay=".6s">
                      <a class="tj-primary-btn" href="contact.html">
                        <span class="btn-text"><span>Request a Call</span></span>
                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                      </a>
                    </div> -->
                  </div>
                </div>
              </div>
            </div>
            <div class="row row-gap-4 rightSwipeWrap">
              <div class="col-lg-4">
                <div class="choose-box right-swipe">
                  <div class="choose-content">
                    <div class="choose-icon">
                      <i class="tji-service-1"></i>
                    </div>
                    <h4 class="title">Cybersecurity Solutions</h4>
                    <p class="desc">We deliver robust cybersecurity solutions to protect businesses against
                      ever-evolving threats
                      and ensure the security of their digital infrastructure</p>
                  </div>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="choose-box right-swipe">
                  <div class="choose-content">
                    <div class="choose-icon">
                      <i class="tji-service-3"></i>
                    </div>
                    <h4 class="title">Cloud Computing</h4>
                    <p class="desc">Our cloud computing services enable organizations to transition to scalable, secure
                      cloud
                      infrastructures, optimizing operations while maintaining data security</p>
                  </div>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="choose-box right-swipe">
                  <div class="choose-content">
                    <div class="choose-icon">
                      <i class="tji-service-4"></i>
                    </div>
                    <h4 class="title">Enterprise Network Infrastructure</h4>
                    <p class="desc">We design and implement secure, scalable network infrastructures to support
                      businesses' digital
                      transformation and growth</p>
                  </div>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="choose-box right-swipe">
                  <div class="choose-content">
                    <div class="choose-icon">
                      <i class="tji-innovative"></i>
                    </div>
                    <h4 class="title">AI Integration</h4>
                    <p class="desc">We help businesses integrate AI solutions that drive efficiency and innovation,
                      ensuring these
                      processes are secure and well-governed</p>
                  </div>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="choose-box right-swipe">
                  <div class="choose-content">
                    <div class="choose-icon">
                      <i class="tji-service-2"></i>
                    </div>
                    <h4 class="title">Software Development</h4>
                    <p class="desc">We provide secure, customized software solutions, including web development,
                      tailored to the
                      unique needs of various industries</p>
                  </div>
                </div>
              </div>

              <div class="col-lg-4">
                <div class="choose-box right-swipe">
                  <div class="choose-content">
                    <div class="choose-icon">
                      <i class="tji-support"></i>
                    </div>
                    <h4 class="title">Training and Consulting</h4>
                    <p class="desc">We provide expert training and consulting across multiple areas, including
                      cybersecurity, cloud
                      computing, and enterprise network infrastructure</p>
                  </div>
                </div>
              </div>
            </div>


          </div>
        </section>
        <!-- end: Choose Section -->

        <!-- start: About Section -->
        <section class="tj-about-section-2 section-gap section-gap-x">
          <div class="container">
            <div class="row">
              <div class="col-xl-6 col-lg-6 order-lg-1 order-2">
                <div class="about-img-area style-2 wow fadeInLeft" data-wow-delay=".3s">
                  <div class="about-img overflow-hidden">
                    <img data-speed=".8" src="{{asset('frontend/assets/images/about/pic1.jpg')}}" alt="">
                  </div>
                  <div class="box-area style-2">
                    <div class="progress-box wow fadeInUp" data-wow-delay=".3s">
                      <h4 class="title">Business Progress</h4>
                      <ul class="tj-progress-list">
                        <li>
                          <h6 class="tj-progress-title">Revenue</h6>
                          <div class="tj-progress">
                            <span class="tj-progress-percent">82%</span>
                            <div class="tj-progress-bar" data-percent="82">
                            </div>
                          </div>
                        </li>
                        <li>
                          <h6 class="tj-progress-title">Satisfaction</h6>
                          <div class="tj-progress">
                            <span class="tj-progress-percent">90%</span>
                            <div class="tj-progress-bar" data-percent="90">
                            </div>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-6 col-lg-6 order-lg-2 order-1">
                <div class="about-content-area">
                  <div class="sec-heading">
                    <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>Get to Know
                      Us</span>
                    <h2 class="sec-title title-anim">Driving Innovation and Excellence for Sustainable Corporate Success
                      <span>Worldwide.</span>
                    </h2>
                  </div>
                </div>
                <div class="about-bottom-area">
                  <div class="mission-vision-box wow fadeInLeft" data-wow-delay=".5s">
                    <h4 class="title">Our Mission</h4>
                    <p class="desc">To empower organizations and individuals by driving digital transformation,
                      enhancing cybersecurity resilience, and
                      delivering innovative and secure technology solutions that support long-term success
                    </p>
                    <ul class="list-items">
                      <li><i class="tji-list"></i>Innovation & Excellence</li>
                      <li><i class="tji-list"></i>Exceptional Customer</li>
                      <li><i class="tji-list"></i>Business Growth</li>
                    </ul>
                  </div>
                  <div class="mission-vision-box wow fadeInRight" data-wow-delay=".5s">
                    <h4 class="title">Our Vision</h4>
                    <p class="desc">To be a trusted leader in digital transformation and technology consulting, helping
                      businesses and individuals achieve
                      sustainable growth through secure, innovative, and cutting-edge solutions

                    </p>
                    <ul class="list-items">
                      <li><i class="tji-list"></i>Global Leadership</li>
                      <li><i class="tji-list"></i>Transformative Impact</li>
                      <li><i class="tji-list"></i>Sustainable Success</li>
                    </ul>
                  </div>
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
        <!-- end: About Section -->



        <!-- start: Testimonial Section -->
        <section class="tj-testimonial-section-2 section-bottom-gap mt-4">
          <div class="container">
            <div class="row row-gap-3">
              <div class="col-lg-6 order-lg-2">
                <div class="testimonial-img-area wow fadeInUp" data-wow-delay=".3s">
                  <div class="testimonial-img">
                    <img src="{{asset('frontend/assets/images/testimonial/pic.jpg')}}" alt="">
                    <div class="sec-heading style-2">
                      <h2 class="sec-title title-anim">Our <span>Commitment.</span></h2>
                    </div>
                  </div>

                </div>
              </div>
              <div class="col-lg-6 order-lg-1">
                <div class="testimonial-wrapper wow fadeInUp" data-wow-delay=".5s">
                  <div class="swiper testimonial-slider-2">
                    <div class="swiper-wrapper">
                      <div class="">
                        <div class="testimonial-item"> <span class="quote-icon"><i class="tji-quote"></i></span>
                          <div class="desc">
                            <p>At Forward Edge Consulting Ltd., we are dedicated to helping businesses not only adopt
                              innovative technologies but also
                              build strong security frameworks to protect their operations. We are committed to
                              empowering individuals and
                              organizations alike, enabling them to advance securely in a rapidly evolving technological
                              landscape.</p>
                          </div>
                          <div class="testimonial-author">

                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>

              </div>
            </div>
        </section>
        <!-- end: Testimonial Section -->

        <!-- start: Team Section -->
        <section class="tj-team-section-3 section-gap section-gap-x">
          <div class="container">
            <div class="row">
              <div class="col-12">
                <div class="sec-heading text-center">
                  <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i> About forward edge
                    consulting</span>
                  <h2 class="sec-title title-anim">Success <span>Stories</span> Fuel our Innovation.</h2>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="desc">
                  <p class="desc wow fadeInUp" data-wow-delay=".6s">
                    At Forward Edge Consulting Ltd., we are dedicated to guiding organizations and individuals through
                    secure and
                    transformative technology adoption. Our mission is to help businesses embrace cutting-edge
                    innovations while ensuring
                    that security remains a top priority throughout their digital transformation journey.

                    As specialists in cybersecurity, cloud solutions, AI integration, blockchain, enterprise network
                    infrastructure, and
                    software development, we provide comprehensive consulting and training services tailored to meet the
                    needs of an
                    evolving digital landscape. We understand that as technology advances, so do the challenges. Our
                    role is to equip our
                    clients with the expertise and support needed to navigate these complexities confidently and
                    securely.

                    Whether you’re looking to optimize operations, protect valuable data, or unlock the potential of new
                    technologies,
                    Forward Edge Consulting Ltd. is your partner in achieving secure, sustainable growth. Together,
                    we’ll build resilient
                    organizations ready to lead in a rapidly advancing digital world.

                  </p>
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
        <!-- end: Team Section -->

        <!-- start: Faq Section -->
        <section class="tj-faq-section section-gap">
          <div class="container">
            <div class="row justify-content-between">
              <div class="col-lg-12">
                <div class="content-wrap">
                  <div class="sec-heading">
                    <span class="sub-title wow fadeInUp" data-wow-delay=".3s"><i class="tji-box"></i>Common
                      Questions</span>
                    <h2 class="sec-title title-anim">Need <span>Help?</span> Start Here...</h2>
                  </div>
                  <p class="desc wow fadeInUp" data-wow-delay=".6s">Are you looking for expert consulting and training
                    in cybersecurity, cloud computing, AI integration, enterprise network
                    infrastructure, or software development? Chat with us on WhatsApp Now! <br>
                    Chat Us On WhatsApp Now</p>
                  <div class="wow fadeInUp" data-wow-delay=".8s">
                    <a class="tj-primary-btn" href="{{ route('contact') }}">
                      <span class="btn-text"><span>Request a Call</span></span>
                      <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </a>
                  </div>
                </div>
              </div>
           

            </div>
          </div>
        </section>
        <!-- end: Faq Section -->

     
@endsection