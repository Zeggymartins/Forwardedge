@extends('user.master_page')
@section('title', 'Contact | Forward Edge Consulting')
@section('main')
@include('user.partials.breadcrumb')
   <!-- start: Contact Top Section -->
        <div class="tj-contact-area section-gap">
          <div class="container">
            <div class="row">
              <div class="col-12">
                <div class="sec-heading text-center">
                  <span class="sub-title wow fadeInUp" data-wow-delay=".1s"><i class="tji-box"></i>Contact info</span>
                  <h2 class="sec-title title-anim"><span>Reach</span> Out to Us</h2>
                </div>
              </div>
            </div>
            <div class="row row-gap-4">
              <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="contact-item style-2 wow fadeInUp" data-wow-delay=".3s">
                  <div class="contact-icon">
                    <i class="tji-location-3"></i>
                  </div>
                  <h3 class="contact-title">Our Location</h3>
                  <p>993 Renner Burg, West Rond, MT 94251-030</p>
                </div>
              </div>
              <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="contact-item style-2 wow fadeInUp" data-wow-delay=".5s">
                  <div class="contact-icon">
                    <i class="tji-envelop"></i>
                  </div>
                  <h3 class="contact-title">Email us</h3>
                  <ul class="contact-list">
                    <li><a href="mailto:support@bexon.com">support@bexon.com</a></li>
                    <li><a href="mailto:info@bexon.com">info@bexon.com</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="contact-item style-2 wow fadeInUp" data-wow-delay=".7s">
                  <div class="contact-icon">
                    <i class="tji-phone"></i>
                  </div>
                  <h3 class="contact-title">Call us</h3>
                  <ul class="contact-list">
                    <li><a href="tel:10095447818">+1 (009) 544-7818</a></li>
                    <li><a href="tel:10098801810">+1 (009) 880-1810</a></li>
                  </ul>
                </div>
              </div>
              <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="contact-item style-2 wow fadeInUp" data-wow-delay=".9s">
                  <div class="contact-icon">
                    <i class="tji-chat"></i>
                  </div>
                  <h3 class="contact-title">Live chat</h3>
                  <ul class="contact-list">
                    <li><a href="mailto:livechat@bexon.com">livechat@bexon.com</a></li>
                    <li class="active"><a href="contact.html">Need help?</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- end: Contact Top Section -->

        <!-- start: Contact Section -->
        <section class="tj-contact-section-2 section-bottom-gap">
          <div class="container">
            <div class="row">
              <div class="col-lg-6">
                <div class="contact-form wow fadeInUp" data-wow-delay=".1s">
                  <h3 class="title">Feel Free to Get in Touch or Visit our Location.</h3>
                  <form id="contact-form">
                    <div class="row">
                      <div class="col-sm-6">
                        <div class="form-input">
                          <input type="text" name="cfName">
                          <label class="cf-label">Full Name <span>*</span></label>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-input">
                          <input type="email" name="cfEmail">
                          <label class="cf-label">Email Address <span>*</span></label>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-input">
                          <input type="tel" name="cfPhone">
                          <label class="cf-label">Phone number <span>*</span></label>
                        </div>
                      </div>
                      <div class="col-sm-6">
                        <div class="form-input">
                          <div class="tj-nice-select-box">
                            <div class="tj-select">
                              <select name="cfSubject">
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
                          <textarea name="cfMessage" id="message"></textarea>
                          <label class="cf-label">Type message <span>*</span></label>
                        </div>
                      </div>
                      <div class="submit-btn">
                        <button class="tj-primary-btn" type="submit">
                          <span class="btn-text"><span>Submit Now</span></span>
                          <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="map-area wow fadeInUp" data-wow-delay=".3s">
                  <iframe
                    src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d316440.5712687838!2d-74.01091796224334!3d40.67186885683901!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sbd!4v1745918398047!5m2!1sen!2sbd"></iframe>
                </div>
              </div>
            </div>
          </div>
        </section>
        <!-- end: Contact Section -->
@endsection