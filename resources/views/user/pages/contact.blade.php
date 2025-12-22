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
                <div class="col-xl-4 col-lg-6 col-sm-6">
                    <div class="contact-item style-2 wow fadeInUp" data-wow-delay=".3s">
                        <div class="contact-icon">
                            <i class="tji-location-3"></i>
                        </div>
                        <h3 class="contact-title">Our Location</h3>
                        <p>Iwaya Road, 58 Iwaya Rd, Yaba, Lagos State</p>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-sm-6">
                    <div class="contact-item style-2 wow fadeInUp" data-wow-delay=".5s">
                        <div class="contact-icon">
                            <i class="tji-envelop"></i>
                        </div>
                        <h3 class="contact-title">Email us</h3>
                        <ul class="contact-list">
                            <li><a href="mailto:info@forwardedgeconsulting.com">info@forwardedgeconsulting.com</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-sm-6">
                    <div class="contact-item style-2 wow fadeInUp" data-wow-delay=".7s">
                        <div class="contact-icon">
                            <i class="tji-phone"></i>
                        </div>
                        <h3 class="contact-title">Call us</h3>
                        <ul class="contact-list">
                            <li><a href="tel:+2347039955591">+234 703 995 5591</a></li>
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
                        @include('user.partials.form-alerts')
                        <form id="contact-form" action="{{ route('contact.store') }}" method="POST">
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
                                <x-honeypot />
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
                <div class="col-lg-6">
                    <div class="map-area wow fadeInUp" data-wow-delay=".3s">
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.1303312003224!2d3.384651975229365!3d6.505181423375437!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b8cf2fb0119cb%3A0x1e51b1250399539d!2s58%20Iwaya%20Rd%2C%20Onike%2C%20Lagos%20101245%2C%20Lagos!5e0!3m2!1sen!2sng!4v1760111656176!5m2!1sen!2sng" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>
    <!-- end: Contact Section -->
@endsection
