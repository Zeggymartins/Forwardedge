@extends('user.master_page')
@section('title', 'Scholarship Application | ' . ($course->title ?? 'Course'))
@section('main')
    @include('user.partials.breadcrumb')

    <section class="full-width tj-page__area section-gap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tj-page__container">
                        <div class="tj-entry__content">
                            <div class="woocommerce">
                                <div class="woo-login-form">
                                    <h3 class="mb-3">Apply for Scholarship</h3>

                                    {{-- Context header --}}
                                    <div class="rounded-12 p-3 mb-4" style="background:#f8f9fa;">
                                        <div class="d-flex flex-wrap gap-3 align-items-center">
                                            <div><strong>Course:</strong> {{ $course->title }}</div>
                                            <div class="vr d-none d-md-block"></div>
                                            <div>
                                                <strong>Schedule:</strong>
                                                {{ optional($schedule->start_date)->format('M j, Y') }}
                                                @if($schedule->start_time)
                                                    • {{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('g:ia') }}
                                                @endif
                                            </div>
                                            <div class="vr d-none d-md-block"></div>
                                            <div><strong>Fee:</strong> FREE (Scholarship)</div>
                                        </div>
                                    </div>

                                    {{-- Flash messages --}}
                                    @if (session('success'))
                                        <div class="woocommerce-message">{{ session('success') }}</div>
                                    @endif
                                    @if (session('error'))
                                        <div class="woocommerce-error">{{ session('error') }}</div>
                                    @endif

                                    {{-- Validation errors --}}
                                    @if ($errors->any())
                                        <ul class="woocommerce-error">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <form class="woocommerce-form woocommerce-form-login login" method="POST"
                                          action="{{ route('scholarships.apply.store', $schedule->id) }}">
                                        @csrf

                                        {{-- Signed-in: show read-only summary. Guest: quick register inputs --}}
                                        @if(auth()->check())
                                            <fieldset class="mb-3" style="border:1px dashed #e1e1e1; padding:12px;">
                                                <legend class="px-2 small text-muted">Your details</legend>
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" class="input-text" value="{{ auth()->user()->name }}" disabled>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Email</label>
                                                        <input type="text" class="input-text" value="{{ auth()->user()->email }}" disabled>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label">Phone</label>
                                                        <input type="text" class="input-text" value="{{ auth()->user()->phone ?? '—' }}" disabled>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        @else
                                            <fieldset class="mb-3" style="border:1px dashed #e1e1e1; padding:12px;">
                                                <legend class="px-2 small text-muted">Quick details (no account yet)</legend>
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label for="guest_name" class="form-label">Full Name <span class="required">*</span></label>
                                                        <input type="text" class="input-text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="guest_email" class="form-label">Email <span class="required">*</span></label>
                                                        <input type="email" class="input-text" name="guest_email" id="guest_email" value="{{ old('guest_email') }}" required>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="guest_phone" class="form-label">Phone <span class="required">*</span></label>
                                                        <input type="tel" class="input-text" name="guest_phone" id="guest_phone" value="{{ old('guest_phone') }}" required>
                                                    </div>
                                                </div>
                                                <div class="form-text mt-2">We’ll create your application with these contact details (and a user for you in the background). You can log in later via password reset.</div>
                                            </fieldset>
                                        @endif

                                        {{-- Application-specific fields --}}
                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="why_join">Why do you want to join this cohort? <span class="required" aria-hidden="true">*</span></label>
                                            <textarea name="why_join" id="why_join" rows="5" required aria-required="true">{{ old('why_join') }}</textarea>
                                        </p>

                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label for="experience">Relevant experience (Optional)</label>
                                            <textarea name="experience" id="experience" rows="4">{{ old('experience') }}</textarea>
                                        </p>

                                        <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                                            <label class="d-flex align-items-start gap-2" for="commitment">
                                                <input type="checkbox" name="commitment" id="commitment" value="1" {{ old('commitment') ? 'checked' : '' }}>
                                                <span>I confirm I can commit to the learning schedule, assignments, and community guidelines for this scholarship.</span>
                                            </label>
                                        </p>

                                        <div class="row form-row align-items-center rg-15">
                                            <div class="col-sm-12">
                                                <button type="submit"
                                                        class="woocommerce-button button woocommerce-form-login__submit"
                                                        name="apply" value="Apply">
                                                    <span class="btn-text"><span>Submit application</span></span>
                                                </button>

                                                <a href="{{ url()->previous() }}" class="button" style="margin-left:8px;">
                                                    Cancel
                                                </a>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
