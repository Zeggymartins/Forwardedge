@extends('user.master_page')
@section('title', 'Payment Successful | Forward Edge Consulting')
@section('breadcrumb_text', 'Your enrollment is locked in. We just emailed your receipt and course access instructions.')

@section('main')
    @include('user.partials.breadcrumb')

    <section class="section-gap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-body text-center py-5 px-4 px-md-5">
                            <div class="mb-4">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success"
                                     style="width:96px;height:96px;">
                                    <i class="tji-check" style="font-size:2.5rem;"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-3">Payment completed!</h2>
                            <p class="text-muted mb-4">
                                Thank you for trusting Forward Edge. We’re getting everything ready—check your inbox for a
                                confirmation email with download links and Drive access.
                            </p>
                            @isset($reference)
                                <p class="mb-4 small text-uppercase text-muted">Reference: <span class="fw-semibold text-dark">{{ $reference }}</span></p>
                            @endisset
                            <div class="d-flex flex-wrap gap-3 justify-content-center">
                                <a href="{{ route('academy') }}" class="tj-primary-btn">
                                    <span class="btn-text"><span>Explore more programs</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </a>
                                <a href="{{ route('shop') }}" class="tj-secondary-btn">
                                    <span class="btn-text"><span>Browse modules</span></span>
                                </a>
                            </div>
                        </div>
                        <div class="bg-success-subtle text-center py-3">
                            <small class="text-success fw-semibold">
                                Didn’t receive the email? Check spam or <a href="{{ route('contact') }}">contact us</a>.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
