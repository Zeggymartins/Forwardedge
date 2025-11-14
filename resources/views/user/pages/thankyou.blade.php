@extends('user.master_page')
@section('title', 'Thank You | Scholarship Application')

@section('main')
    @include('user.partials.breadcrumb')

    <section class="full-width tj-page__area section-gap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10">
                    <div class="thankyou-hero position-relative overflow-hidden mb-5">
                        <div class="spark one"></div>
                        <div class="spark two"></div>
                        <div class="thankyou-hero__body">
                            <div class="icon-burst">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
                                    <path d="M20 7L10 17l-6-6" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <div>
                                <p class="eyebrow">Application received</p>
                                <h1>You're officially on our radar! 🔥</h1>
                                <p class="lead mb-0">
                                    Thanks for raising your hand. Our admissions team is reviewing your story, motivation,
                                    and readiness. Watch your inbox — you’ll hear from us soon.
                                </p>
                            </div>
                        </div>
                        <div class="thankyou-hero__stats">
                            <div>
                                <span>Program</span>
                                <strong>{{ $course->title ?? 'Selected Course' }}</strong>
                            </div>
                            <div>
                                <span>Next Cohort</span>
                                <strong>
                                    @if(!empty($schedule?->start_date))
                                        {{ \Illuminate\Support\Carbon::parse($schedule->start_date)->format('M j, Y') }}
                                    @else
                                        To be announced
                                    @endif
                                </strong>
                            </div>
                            <div>
                                <span>Status</span>
                                <span class="badge bg-white text-primary-subtle">Pending review</span>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6 col-xl-4">
                            <div class="glass-card h-100">
                                <p class="label">Right now</p>
                                <h4>Application under review</h4>
                                <p class="mb-0 text-muted">We verify availability, readiness, and fit for the bootcamp. This part can take 5–7 working days.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-xl-4">
                            <div class="glass-card h-100">
                                <p class="label">If shortlisted</p>
                                <h4>Expect a quick interview</h4>
                                <p class="mb-0 text-muted">We’ll schedule a fast call to understand your goals and confirm your commitment to the workload.</p>
                            </div>
                        </div>
                        <div class="col-md-12 col-xl-4">
                            <div class="glass-card h-100">
                                <p class="label">Stay prepared</p>
                                <h4>Bootcamp starter pack</h4>
                                <p class="mb-3 text-muted">You’ll receive laptop setup guides, community invites, and pre-work once your slot is confirmed.</p>
                                <a href="{{ route('contact') }}" class="tj-secondary-btn w-100 justify-content-center">
                                    Need help? Contact us
                                    <span class="btn-icon"><i class="tji-arrow-up-right"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-card mb-4">
                        <h3 class="h5 fw-bold mb-4">Your path to joining the cohort</h3>
                        <div class="timeline">
                            <div class="timeline-step completed">
                                <div class="dot"></div>
                                <div>
                                    <h5>Application submitted</h5>
                                    <p>We already emailed a receipt. Didn’t see it? Check promotions/spam.</p>
                                </div>
                            </div>
                            <div class="timeline-step">
                                <div class="dot"></div>
                                <div>
                                    <h5>Review & shortlist</h5>
                                    <p>We match your answers against available slots. Cohorts are small and hands-on.</p>
                                </div>
                            </div>
                            <div class="timeline-step">
                                <div class="dot"></div>
                                <div>
                                    <h5>Decision email</h5>
                                    <p>If selected, you’ll receive onboarding instructions, community invites, and pre-labs.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4 align-items-stretch">
                        <div class="col-lg-6">
                            <div class="focus-card h-100">
                                <h4>Power moves while you wait</h4>
                                <ul class="mb-0">
                                    <li>Rewatch the course outline & note specific questions you want answered.</li>
                                    <li>Block out the cohort dates on your calendar, including evenings/weekends.</li>
                                    <li>Update your laptop OS, storage, and internet plan (labs can be resource-heavy).</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="focus-card h-100">
                                <h4>Show up everywhere</h4>
                                <p class="mb-3">Follow our social handles for live updates, alumni wins, and behind-the-scenes teaching moments.</p>
                                <div class="d-flex flex-wrap gap-3">
                                    <a href="https://www.instagram.com/forwardedge_consultingltd" target="_blank" class="tj-secondary-btn">
                                        Instagram
                                        <span class="btn-icon"><i class="tji-arrow-up-right"></i></span>
                                    </a>
                                    <a href="https://x.com/ForwardEdgeNg" target="_blank" class="tj-secondary-btn">
                                        Twitter / X
                                        <span class="btn-icon"><i class="tji-arrow-up-right"></i></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cta-card text-center">
                        <h4 class="mb-2">Want to boost your chances?</h4>
                        <p class="mb-3 text-muted">Reply to the confirmation email with a short video or thread showing your current learning efforts. Passion stands out.</p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="{{ isset($course->slug) ? route('course.show', $course->slug) : route('academy') }}" class="tj-secondary-btn">
                                Explore the course again
                                <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                            </a>
                            <a href="{{ route('contact') }}" class="tj-primary-btn">
                                Talk to our team
                            </a>
                        </div>
                    </div>

                    <p class="text-center text-muted small mt-4">
                        Need urgent support? Email <a href="mailto:info@forwardedgeconsulting.com">info@forwardedgeconsulting.com</a> or use the contact form.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .thankyou-hero {
            background: linear-gradient(120deg, #1d4ed8, #9333ea);
            border-radius: 32px;
            padding: 2.5rem;
            color: #fff;
            box-shadow: 0 30px 80px rgba(30,64,175,.35);
        }
        .thankyou-hero__body {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }
        .thankyou-hero__body h1 {
            font-weight: 700;
            font-size: clamp(1.9rem, 3vw, 3rem);
            margin-bottom: .75rem;
        }
        .thankyou-hero__body .lead {
            color: rgba(255,255,255,.9);
        }
        .icon-burst {
            width: 70px;
            height: 70px;
            border-radius: 22px;
            background: rgba(255,255,255,.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(6px);
        }
        .thankyou-hero__stats {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
        }
        .thankyou-hero__stats > div {
            background: rgba(255,255,255,.12);
            border-radius: 18px;
            padding: 1rem 1.2rem;
            border: 1px solid rgba(255,255,255,.2);
        }
        .thankyou-hero__stats span {
            display: block;
            font-size: .85rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: .35rem;
            color: rgba(255,255,255,.8);
        }
        .eyebrow {
            text-transform: uppercase;
            letter-spacing: .3em;
            font-size: .75rem;
            font-weight: 600;
            color: rgba(255,255,255,.8);
        }
        .spark {
            position: absolute;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(255,255,255,.45), transparent 70%);
            border-radius: 50%;
            filter: blur(10px);
        }
        .spark.one { top: -40px; right: -30px; }
        .spark.two { bottom: -60px; left: -40px; }

        .glass-card {
            border-radius: 24px;
            padding: 1.5rem;
            border: 1px solid #e2e8ff;
            background: #fff;
            box-shadow: 0 15px 40px rgba(15,23,42,.08);
        }
        .glass-card .label {
            text-transform: uppercase;
            font-size: .78rem;
            letter-spacing: .12em;
            color: #7c8ab8;
            margin-bottom: .65rem;
        }

        .timeline-card {
            border-radius: 28px;
            border: 1px solid #f1f5ff;
            padding: 2rem;
            background: #fafcff;
        }
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            position: relative;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 4px;
            bottom: 4px;
            width: 2px;
            background: linear-gradient(#93c5fd, #dbeafe);
        }
        .timeline-step {
            position: relative;
            padding-left: 3rem;
        }
        .timeline-step .dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 4px solid #fff;
            background: #93c5fd;
            position: absolute;
            left: 0;
            top: .4rem;
            box-shadow: 0 0 0 4px rgba(147,197,253,.3);
        }
        .timeline-step.completed .dot {
            background: #0ea5e9;
        }

        .focus-card {
            border-radius: 24px;
            padding: 1.75rem;
            border: 1px solid #ecf1ff;
            background: linear-gradient(180deg, #fff 0%, #f9fbff 100%);
            box-shadow: 0 10px 30px rgba(99,102,241,.08);
        }
        .focus-card ul {
            padding-left: 1rem;
        }
        .focus-card li {
            margin-bottom: .6rem;
        }

        .cta-card {
            border-radius: 28px;
            padding: 2rem;
            border: 1px dashed #cbd5ff;
            background: #eef2ff;
            box-shadow: inset 0 0 50px rgba(99,102,241,.12);
        }

        .tj-secondary-btn {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .95rem 1.2rem;
            border-radius: .85rem;
            border: 1px solid rgba(15,23,42,.08);
            background: #fff;
            color: #0f172a;
            font-weight: 600;
            text-decoration: none;
            transition: .2s ease-in-out;
        }
        .tj-secondary-btn:hover {
            transform: translateY(-2px);
            border-color: #0f172a;
        }

        @media (max-width: 768px) {
            .thankyou-hero {
                padding: 1.75rem;
            }
            .thankyou-hero__body {
                flex-direction: column;
                text-align: center;
            }
            .icon-burst {
                margin-bottom: .75rem;
            }
        }
    </style>
@endpush
