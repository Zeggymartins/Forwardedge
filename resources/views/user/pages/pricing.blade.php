@extends('user.master_page')
@section('title', 'Pricing | Forward Edge Consulting')

@section('main')
    @include('user.partials.breadcrumb')

    <section class="tj-pricing-section-2 section-gap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="sec-heading text-center">
                        <span class="sub-title wow fadeInUp" data-wow-delay=".1s">
                            <i class="tji-box"></i>Payment Options
                        </span>
                        <h2 class="sec-title title-anim">Choose Your <span>Plan.</span></h2>
                    </div>
                </div>
            </div>

            <div class="row row-gap-4 justify-content-center">
                {{-- Full Payment --}}
                <div class="col-xl-5 col-md-6">
                    <div class="pricing-box wow fadeInUp" data-wow-delay=".5s">
                        <div class="pricing-header">
                            <h4 class="package-name">Full Payment</h4>
                            <div class="package-desc">
                                <p>Pay 100% upfront and secure your seat instantly.</p>
                            </div>
                           <div class="package-price text-center">
    <span class="package-currency">₦</span>
    <span 
        class="price-number fw-bold d-inline-block text-wrap 
               fs-2 fs-md-1 fs-lg-1">
        {{ number_format($schedule->price, 2) }}
    </span>
    <span class="package-period d-block small">/one-time</span>
</div>


                            <div class="pricing-btn">
                                <a href="#" class="text-btn choose-plan" data-schedule-id="{{ $schedule->id }}"
                                    data-plan="full">
                                    <span class="btn-text"><span>Choose Full Plan</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Partial Payment --}}
                <div class="col-xl-5 col-md-6">
                    <div class="pricing-box active wow fadeInUp" data-wow-delay=".7s">
                        <div class="pricing-header">
                            <h4 class="package-name">Partial Payment</h4>
                            <div class="package-desc">
                                <p>Pay 70% upfront, balance before bootcamp starts.</p>
                            </div>
                        <div class="package-price text-center">
    <span class="package-currency">₦</span>
    <span 
        class="price-number fw-bold d-inline-block text-wrap 
               fs-2 fs-md-1 fs-lg-1">
        {{ number_format($schedule->price * 0.7, 2) }}
    </span>
    <span class="package-period d-block small">/initial</span>
</div>

                            <div class="pricing-btn">
                                <a href="#" class="text-btn choose-plan" data-schedule-id="{{ $schedule->id }}"
                                    data-plan="partial">
                                    <span class="btn-text"><span>Choose Partial Plan</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

  
@endsection
@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    $('.choose-plan').on('click', function (e) {
        e.preventDefault(); // stop page reload from <a href="#">

        console.log("✅ Choose plan clicked");

        let scheduleId = $(this).data('schedule-id');
        let plan = $(this).data('plan');

        // give user feedback
        $(this).find('.btn-text span').text('Processing...');

        $.ajax({
            url: "{{ route('enroll.store') }}",
            method: "POST",
            data: {
                schedule_id: scheduleId,
                payment_plan: plan,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                console.log("✅ Server response:", response);
                if (response.authorization_url) {
                    window.location.href = response.authorization_url;
                } else {
                    alert('Unexpected response. Please try again.');
                }
            },
            error: function (xhr) {
                console.error("❌ AJAX error:", xhr);
                let msg = 'Something went wrong. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                }
                alert(msg);
            }
        });
    });
});
</script>
@endpush

