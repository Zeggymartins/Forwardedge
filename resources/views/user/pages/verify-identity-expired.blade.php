@extends('user.master_page')

@section('title', 'Verification Link Expired - Forward Edge')

{{-- @section('hide_header', true) --}}

@push('styles')
<style>
    .verify-container {
        min-height: 100vh;
        /* background: linear-gradient(135deg, #050b1f 0%, #0c1530 100%); */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }
    .verify-card {
        background: #0c1530;
        border-radius: 20px;
        box-shadow: 0 25px 70px rgba(0,0,0,0.35);
        max-width: 500px;
        margin: 0 auto;
        padding: 50px;
        text-align: center;
    }
    .verify-card img {
        max-width: 120px;
        margin-bottom: 30px;
    }
    .verify-card h1 {
        color: #f8fafc;
        font-size: 28px;
        margin: 0 0 20px;
    }
    .verify-card p {
        color: #94a3b8;
        font-size: 16px;
        line-height: 1.7;
        margin: 0 0 30px;
    }
    .icon-expired {
        font-size: 64px;
        color: #ef4444;
        margin-bottom: 20px;
    }
    .btn-contact {
        background: linear-gradient(135deg, #0891b2, #6366f1);
        border: none;
        color: #fff;
        padding: 14px 40px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
    }
</style>
@endpush

@section('main')
<div class="verify-container">
    <div class="verify-card">
        <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Forward Edge">
        <i class="bi bi-clock-history icon-expired"></i>
        <h1>Link Expired</h1>
        <p>
            Your verification link has expired. Verification links are valid for 7 days.
            Please contact our support team to receive a new verification link.
        </p>
        <a href="mailto:info@forwardedgeconsulting.com" class="btn-contact">
            <i class="bi bi-envelope me-2"></i>Contact Support
        </a>
    </div>
</div>
@endsection
