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
                            <div class="package-price">
                                <span class="package-currency">₦</span>
                                <span class="price-number">{{ number_format($schedule->price, 2) }}</span>
                                <span class="package-period">/one-time</span>
                            </div>
                            <div class="pricing-btn">
                                <a class="text-btn choose-plan" 
                                   href="{{ route('enroll.store') }}" 
                                   data-schedule-id="{{ $schedule->id }}" 
                                   data-plan="full">
                                    <span class="btn-text"><span>Choose Full Plan</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div>
                        </div>
                        <div class="list-items">
                            <ul>
                                <li><i class="tji-list"></i>100% of course fee paid once</li>
                                <li><i class="tji-list"></i>No pending balance</li>
                                <li><i class="tji-list"></i>Instant enrollment confirmation</li>
                            </ul>
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
                            <div class="package-price">
                                <span class="package-currency">₦</span>
                                <span class="price-number">{{ number_format($schedule->price * 0.7, 2) }}</span>
                                <span class="package-period">/initial</span>
                            </div>
                            <div class="pricing-btn">
                                <a class="text-btn choose-plan" 
                                   href="{{ route('enroll.store') }}" 
                                   data-schedule-id="{{ $schedule->id }}" 
                                   data-plan="partial">
                                    <span class="btn-text"><span>Choose Partial Plan</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                            </div>
                        </div>
                        <div class="list-items">
                            <ul>
                                <li><i class="tji-list"></i>70% upfront payment</li>
                                <li><i class="tji-list"></i>Remaining 30% due before start date</li>
                                <li><i class="tji-list"></i>Seat reserved once first payment clears</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
