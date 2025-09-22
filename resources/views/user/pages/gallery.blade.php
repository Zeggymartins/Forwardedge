@extends('user.master_page')
@section('title', 'Gallery | Forward Edge Consulting')
@section('main')
@include('user.partials.breadcrumb')
 <!-- start: Team Section -->
        <section class="tj-team-section">
          <div class="container">
            <div class="row">
              <div class="col-12">
                <div class="sec-heading text-center">
                  <span class="sub-title wow fadeInUp" data-wow-delay=".1s"><i class="tji-box"></i>Meet Our Team</span>
                  <h2 class="sec-title title-anim">People Behind <span>Bexon.</span></h2>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-sm-6">
                <div class="team-item wow fadeInUp" data-wow-delay=".1s">
                  <div class="team-img">
                    <div class="team-img-inner">
                      <img src="{{asset('frontend/assets/images/team/team-1.webp')}}" alt="">
                    </div>
                    <div class="social-links">
                      <ul>
                        <li><a href="https://www.facebook.com/" target="_blank"><i
                              class="fa-brands fa-facebook-f"></i></a>
                        </li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                              class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                              class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="team-content">
                    <h4 class="title"><a href="team-details.html">Eade Marren</a></h4>
                    <span class="designation">Chief Executive</span>
                    <a class="mail-at" href="mailto:info@bexon.com"><i class="tji-at"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-sm-6">
                <div class="team-item wow fadeInUp" data-wow-delay=".3s">
                  <div class="team-img">
                    <div class="team-img-inner">
                      <img src="{{asset('frontend/assets/images/team/team-2.webp')}}" alt="">
                    </div>
                    <div class="social-links">
                      <ul>
                        <li><a href="https://www.facebook.com/" target="_blank"><i
                              class="fa-brands fa-facebook-f"></i></a>
                        </li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                              class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                              class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="team-content">
                    <h4 class="title"><a href="team-details.html">Savannah Ngueen</a></h4>
                    <span class="designation">Operations Head</span>
                    <a class="mail-at" href="mailto:info@bexon.com"><i class="tji-at"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-sm-6">
                <div class="team-item wow fadeInUp" data-wow-delay=".5s">
                  <div class="team-img">
                    <div class="team-img-inner">
                      <img src="{{asset('frontend/assets/images/team/team-3.webp')}}" alt="">
                    </div>
                    <div class="social-links">
                      <ul>
                        <li><a href="https://www.facebook.com/" target="_blank"><i
                              class="fa-brands fa-facebook-f"></i></a>
                        </li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                              class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                              class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="team-content">
                    <h4 class="title"><a href="team-details.html">Kristin Watson</a></h4>
                    <span class="designation">Marketing Lead</span>
                    <a class="mail-at" href="mailto:info@bexon.com"><i class="tji-at"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-sm-6">
                <div class="team-item wow fadeInUp" data-wow-delay=".7s">
                  <div class="team-img">
                    <div class="team-img-inner">
                      <img src="{{asset('frontend/assets/images/team/team-4.webp')}}" alt="">
                    </div>
                    <div class="social-links">
                      <ul>
                        <li><a href="https://www.facebook.com/" target="_blank"><i
                              class="fa-brands fa-facebook-f"></i></a>
                        </li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                              class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                              class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="team-content">
                    <h4 class="title"><a href="team-details.html">Darlene Robertson</a></h4>
                    <span class="designation">Business Director</span>
                    <a class="mail-at" href="mailto:info@bexon.com"><i class="tji-at"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-sm-6">
                <div class="team-item wow fadeInUp" data-wow-delay=".1s">
                  <div class="team-img">
                    <div class="team-img-inner">
                      <img src="{{asset('frontend/assets/images/team/team-5.webp')}}" alt="">
                    </div>
                    <div class="social-links">
                      <ul>
                        <li><a href="https://www.facebook.com/" target="_blank"><i
                              class="fa-brands fa-facebook-f"></i></a>
                        </li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                              class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                              class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="team-content">
                    <h4 class="title"><a href="team-details.html">Darlene Robertson</a></h4>
                    <span class="designation">Business Director</span>
                    <a class="mail-at" href="mailto:info@bexon.com"><i class="tji-at"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-sm-6">
                <div class="team-item wow fadeInUp" data-wow-delay=".3s">
                  <div class="team-img">
                    <div class="team-img-inner">
                      <img src="{{asset('frontend/assets/images/team/team-6.webp')}}" alt="">
                    </div>
                    <div class="social-links">
                      <ul>
                        <li><a href="https://www.facebook.com/" target="_blank"><i
                              class="fa-brands fa-facebook-f"></i></a>
                        </li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                              class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                              class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="team-content">
                    <h4 class="title"><a href="team-details.html">Kristin Watson</a></h4>
                    <span class="designation">Marketing Lead</span>
                    <a class="mail-at" href="mailto:info@bexon.com"><i class="tji-at"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-sm-6">
                <div class="team-item wow fadeInUp" data-wow-delay=".5s">
                  <div class="team-img">
                    <div class="team-img-inner">
                      <img src="{{asset('frontend/assets/images/team/team-7.webp')}}" alt="">
                    </div>
                    <div class="social-links">
                      <ul>
                        <li><a href="https://www.facebook.com/" target="_blank"><i
                              class="fa-brands fa-facebook-f"></i></a>
                        </li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                              class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                              class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="team-content">
                    <h4 class="title"><a href="team-details.html">Savannah Ngueen</a></h4>
                    <span class="designation">Operations Head</span>
                    <a class="mail-at" href="mailto:info@bexon.com"><i class="tji-at"></i></a>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-sm-6">
                <div class="team-item wow fadeInUp" data-wow-delay=".7s">
                  <div class="team-img">
                    <div class="team-img-inner">
                      <img src="{{asset('frontend/assets/images/team/team-8.webp')}}" alt="">
                    </div>
                    <div class="social-links">
                      <ul>
                        <li><a href="https://www.facebook.com/" target="_blank"><i
                              class="fa-brands fa-facebook-f"></i></a>
                        </li>
                        <li><a href="https://www.instagram.com/" target="_blank"><i
                              class="fa-brands fa-instagram"></i></a>
                        </li>
                        <li><a href="https://x.com/" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/" target="_blank"><i
                              class="fa-brands fa-linkedin-in"></i></a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div class="team-content">
                    <h4 class="title"><a href="team-details.html">Eade Marren</a></h4>
                    <span class="designation">Chief Executive</span>
                    <a class="mail-at" href="mailto:info@bexon.com"><i class="tji-at"></i></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- end: Team Section -->
@endsection