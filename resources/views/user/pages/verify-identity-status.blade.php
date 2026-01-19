@extends('user.master_page')

@section('title', 'Verification Status - Forward Edge')

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
    .icon-pending {
        font-size: 64px;
        color: #f59e0b;
        margin-bottom: 20px;
    }
    .icon-verified {
        font-size: 64px;
        color: #22c55e;
        margin-bottom: 20px;
    }
    .enrollment-id {
        background: linear-gradient(135deg, #0891b2, #6366f1);
        border-radius: 12px;
        padding: 20px;
        margin: 20px 0;
    }
    .enrollment-id .label {
        color: #e2e8f0;
        font-size: 14px;
        margin-bottom: 8px;
    }
    .enrollment-id .id {
        color: #fff;
        font-size: 28px;
        font-weight: bold;
        letter-spacing: 2px;
    }
    .btn-home {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: #fff;
        padding: 14px 40px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }
    .btn-home:hover {
        background: rgba(255,255,255,0.15);
        color: #fff;
    }
</style>
@endpush

@section('main')
<div class="verify-container">
    <div class="verify-card">
        <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Forward Edge">

        @if($user->verification_status === 'pending')
            <i class="bi bi-hourglass-split icon-pending"></i>
            <h1>Verification Processing</h1>
            <p>
                Thanks for submitting your documents. We are running automated checks
                and will update your access shortly.
            </p>
        @elseif($user->verification_status === 'verified')
            <i class="bi bi-patch-check-fill icon-verified"></i>
            <h1>You're Verified!</h1>
            <p>
                Your identity has been verified. Welcome to Forward Edge Academy!
            </p>
            @if($user->enrollment_id)
                <div class="enrollment-id">
                    <div class="label">Your Enrollment ID</div>
                    <div class="label">Please make sure you coppy the enrollment id as it will be used later</div>
                    <div class="id">{{ $user->enrollment_id }}</div>
                </div>
            @endif
        @endif

        <a href="/" class="btn-home">
            <i class="bi bi-house me-2"></i>Go to Homepage
        </a>
    </div>
</div>
@endsection
