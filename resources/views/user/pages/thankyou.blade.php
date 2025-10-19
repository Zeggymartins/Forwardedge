@extends('user.master_page')
@section('title', 'Thank You | Scholarship Application')
@section('main')
    @include('user.partials.breadcrumb')

    <section class="full-width tj-page__area section-gap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-10">
                    <div class="tj-page__container">
                        <div class="tj-entry__content">

                            {{-- Confetti-ish header card --}}
                            <div class="p-4 p-md-5 rounded-4 border position-relative overflow-hidden" style="background: radial-gradient(1200px 600px at 90% -20%, #e9f3ff 0%, transparent 60%), linear-gradient(180deg, #fff 0%, #f9fbff 100%); box-shadow: 0 20px 50px rgba(28,50,84,.06)">
                                <div class="position-absolute opacity-25" style="right:-80px;top:-60px;width:260px;height:260px; background: conic-gradient(from 90deg,#0d6efd33,#6f42c133,#20c99733,#0d6efd33); filter: blur(20px); border-radius: 50%;"></div>

                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;background:#e8f1ff">
                                        {{-- checkmark icon --}}
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                                            <path d="M20 7L10 17l-6-6" stroke="#0d6efd" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h1 class="mb-1 fw-bold" style="letter-spacing:.2px">Thank you for applying! 🎉</h1>
                                        <p class="mb-0 text-muted">We’ve received your scholarship application and just emailed you a confirmation.</p>
                                    </div>
                                </div>

                                {{-- quick summary --}}
                                <div class="row g-3 mt-2">
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 border bg-white h-100">
                                            <div class="text-muted small mb-1">Program</div>
                                            <div class="fw-semibold">{{ $course->title ?? 'Selected Course' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 border bg-white h-100">
                                            <div class="text-muted small mb-1">Schedule</div>
                                            <div class="fw-semibold">
                                                @if(!empty($schedule?->start_date))
                                                    {{ \Illuminate\Support\Carbon::parse($schedule->start_date)->format('M j, Y') }}
                                                    @if(!empty($schedule?->start_time)) • {{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('g:ia') }} @endif
                                                @else
                                                    To be announced
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 rounded-3 border bg-white h-100">
                                            <div class="text-muted small mb-1">Status</div>
                                            <span class="badge rounded-pill" style="background:#ecf7ff;color:#0d6efd">Pending Review</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- what’s next --}}
                            <div class="mt-4 row g-4">
                                <div class="col-lg-7">
                                    <div class="p-4 rounded-4 border h-100">
                                        <h3 class="h5 fw-bold mb-3">What happens next</h3>
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex gap-3 mb-3">
                                                <span class="rounded-3 d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:#f4f6ff">
                                                    <i class="tji-check" style="color:#0d6efd"></i>
                                                </span>
                                                <div>
                                                    <div class="fw-semibold">Application review</div>
                                                    <div class="text-muted small">Our team reviews all submissions fairly and selects candidates based on eligibility & commitment.</div>
                                                </div>
                                            </li>
                                            <li class="d-flex gap-3 mb-3">
                                                <span class="rounded-3 d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:#f4f6ff">
                                                    <i class="tji-mail" style="color:#0d6efd"></i>
                                                </span>
                                                <div>
                                                    <div class="fw-semibold">Email notification</div>
                                                    <div class="text-muted small">You’ll receive an email with the outcome and next steps. Keep an eye on your inbox (and spam).</div>
                                                </div>
                                            </li>
                                            <li class="d-flex gap-3">
                                                <span class="rounded-3 d-inline-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;background:#f4f6ff">
                                                    <i class="tji-bolt" style="color:#0d6efd"></i>
                                                </span>
                                                <div>
                                                    <div class="fw-semibold">No scholarship? You can still join</div>
                                                    <div class="text-muted small">Secure your seat in <strong>Foundations</strong> for <strong>₦100,000 / $67</strong> and continue with subsidized specializations (₦50,000 / $33 each).</div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                {{-- CTAs --}}
                                <div class="col-lg-5">
                                    <div class="p-4 rounded-4 h-100" style="background:linear-gradient(135deg,#0d6efd 0%,#6f42c1 100%); color:#fff;">
                                        <h3 class="h5 fw-bold mb-3">Keep your momentum</h3>
                                        <p class="mb-4 opacity-90">Don’t wait for results to start building skills. Enroll now or explore specializations.</p>

                                        <div class="d-grid gap-2">
                                            <a class="tj-primary-btn" href="">
                                                <span class="btn-text"><span>Enroll in Foundations Now</span></span>
                                                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                            </a>
                                            <a class="tj-secondary-btn" style="--tw:rgba(255,255,255,.14)" href="{{ $specializationsUrl ?? (isset($course->slug) ? route('course.show', $course->slug) . '#specializations' : '#') }}">
                                                <span class="btn-text"><span>Learn About Specializations</span></span>
                                                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                            </a>
                                            <a class="button mt-2" href="{{ route('home') }}" style="background:#ffffff1a;border-color:#ffffff33;color:#fff">Return Home</a>
                                        </div>

                                        {{-- reassurance --}}
                                        <div class="d-flex align-items-center gap-2 mt-4 small" style="opacity:.9">
                                            <i class="tji-shield-check"></i>
                                            <span>Secure checkout • No hidden fees</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- helpful links --}}
                            <div class="mt-4 p-4 rounded-4 border">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-8">
                                        <div class="fw-semibold">Didn’t get an email?</div>
                                        <div class="text-muted small">Check your spam folder or whitelist our address. You can also view the course page for updates.</div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <a class="button" href="{{ isset($course->slug) ? route('course.show', $course->slug) : '#' }}">
                                            View Course Page
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- tiny footer note --}}
                            <div class="text-center text-muted small mt-4">
                                Need help? <a href="{{ route('contact') }}" class="link">Contact support</a>.
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('styles')
        <style>
            .tj-secondary-btn {
                display:inline-flex;align-items:center;gap:.5rem;
                padding: .85rem 1.1rem;border-radius:.75rem;border:1px solid #ffffff33;
                background: var(--tw,#f1f5f9); color:#fff; text-decoration:none;
                transition: .2s ease-in-out;
            }
            .tj-secondary-btn:hover { transform: translateY(-1px); border-color:#fff; }
            .tj-secondary-btn .btn-icon{display:inline-flex;margin-left:.25rem}
        </style>
    @endpush
@endsection
