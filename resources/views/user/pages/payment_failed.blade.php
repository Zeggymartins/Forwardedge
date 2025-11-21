@extends('user.master_page')
@section('title', 'Payment Failed | Forward Edge Consulting')
@section('breadcrumb_text', 'The transaction did not go through. You can try again or reach our team for a manual invoice.')

@section('main')
    @include('user.partials.breadcrumb')

    <section class="section-gap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-body text-center py-5 px-4 px-md-5">
                            <div class="mb-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger"
                                     style="width:96px;height:96px;">
                                    <i class="tji-close" style="font-size:2.5rem;"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-3">Payment could not be completed</h2>
                            <p class="text-muted mb-4">
                                Your bank didn’t authorize this transaction. Please confirm your details and try again. If the issue
                                persists, let us know and we’ll help you enroll manually.
                            </p>
                            <div class="d-flex flex-wrap gap-3 justify-content-center">
                                <a href="{{ route('user.cart.index') }}" class="tj-primary-btn">
                                    <span class="btn-text"><span>Return to cart</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-left-long"></i></span>
                                </a>
                                <a href="{{ route('contact') }}" class="tj-secondary-btn">
                                    <span class="btn-text"><span>Contact support</span></span>
                                </a>
                            </div>
                        </div>
                        <div class="bg-danger-subtle text-center py-3">
                            <small class="text-danger fw-semibold">
                                Need help fast? Email <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
