@extends('user.master_page')
@section('title', 'Scholarship Application | ' . ($course->title ?? 'Course'))

@php
    $options = $formOptions ?? config('scholarship.form_options');
    $selectedTools = (array) old('tech_tools', []);
    $auth = auth()->user();
@endphp

@section('main')
    @include('user.partials.breadcrumb')

    <section class="full-width tj-page__area section-gap">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="application-hero mb-5">
                        <div class="application-hero__content">
                            <p class="eyebrow mb-2">Cybersecurity Scholarship Bootcamp</p>
                            <h1 class="mb-3">Tell us why you’re ready to do the hard work.</h1>
                            <p class="mb-0">
                                We’re hand-picking the most committed learners for an intensive, hands-on experience.
                                Share honest answers so our team can understand your motivation, readiness, and technical fit.
                            </p>
                        </div>
                        <div class="application-hero__meta">
                            <div>
                                <span class="label">Course</span>
                                <strong>{{ $course->title }}</strong>
                            </div>
                            <div>
                                <span class="label">Next Cohort</span>
                                <strong>
                                    @if($schedule->start_date)
                                        {{ $schedule->start_date->format('M j, Y') }}
                                    @else
                                        To be announced
                                    @endif
                                </strong>
                            </div>
                            <div>
                                <span class="label">Tuition</span>
                                <strong>₦0 (Full Scholarship)</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Flash + validation --}}
                    @if (session('success'))
                        <div class="alert alert-success rounded-4 border-0 shadow-sm mb-4">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">{{ session('error') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                            <p class="mb-2 fw-semibold">Please fix the following:</p>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="application-form" method="POST"
                          action="{{ route('scholarships.apply.store', $schedule->id) }}">
                        @csrf

                        {{-- Personal --}}
                        <div class="application-section">
                            <div class="application-section__header">
                                <span class="pill">1</span>
                                <div>
                                    <h3>Personal Information</h3>
                                    <p class="mb-0">Let’s start with the basics so we can reach you.</p>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="required">*</span></label>
                                    <input type="text" name="full_name" class="form-control"
                                           value="{{ old('full_name', $auth->name ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address <span class="required">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email', $auth->email ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number (WhatsApp preferred) <span class="required">*</span></label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone', $auth->phone ?? '') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Gender <span class="required">*</span></label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['genders'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('gender') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Age Range <span class="required">*</span></label>
                                    <select name="age_range" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['age_ranges'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('age_range') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Location / State of Residence <span class="required">*</span></label>
                                    <input type="text" name="location" class="form-control"
                                           value="{{ old('location') }}" placeholder="e.g. Lekki, Lagos" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Current Status <span class="required">*</span></label>
                                    <select name="occupation_status" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['occupation_statuses'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('occupation_status') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Education --}}
                        <div class="application-section">
                            <div class="application-section__header">
                                <span class="pill">2</span>
                                <div>
                                    <h3>Educational Background</h3>
                                    <p class="mb-0">Help us understand your academic context.</p>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Highest level of education <span class="required">*</span></label>
                                    <select name="education_level" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['education_levels'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('education_level') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Field of study / background (if any)</label>
                                    <input type="text" name="education_field" class="form-control"
                                           value="{{ old('education_field') }}" placeholder="e.g. Computer Science">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Are you currently in school? <span class="required">*</span></label>
                                    <select name="education_currently_in_school" class="form-select toggle-field" data-toggle-target="school-fields" required>
                                        <option value="">Select</option>
                                        @foreach(($options['yes_no'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('education_currently_in_school') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 collapse-target school-fields {{ old('education_currently_in_school') === 'yes' ? 'is-visible' : '' }}">
                                    <label class="form-label">Institution & Level</label>
                                    <input type="text" name="education_institution" class="form-control mb-2"
                                           value="{{ old('education_institution') }}" placeholder="Institution name">
                                    <input type="text" name="education_institution_level" class="form-control"
                                           value="{{ old('education_institution_level') }}" placeholder="Level / year">
                                </div>
                            </div>
                        </div>

                        {{-- Commitment --}}
                        <div class="application-section">
                            <div class="application-section__header">
                                <span class="pill">3</span>
                                <div>
                                    <h3>Commitment & Readiness</h3>
                                    <p class="mb-0">This bootcamp is intense. Show us you’re ready.</p>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Can you attend all live sessions & complete assignments? <span class="required">*</span></label>
                                    <select name="commit_available" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['commit_availability'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('commit_available') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Hours per week you can dedicate <span class="required">*</span></label>
                                    <select name="commit_hours" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['commit_hours'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('commit_hours') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">What will you do to stay consistent even when it gets intense? <span class="required">*</span></label>
                                    <textarea name="commit_strategy" rows="4" class="form-control" required>{{ old('commit_strategy') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Technical --}}
                        <div class="application-section">
                            <div class="application-section__header">
                                <span class="pill">4</span>
                                <div>
                                    <h3>Technical Readiness</h3>
                                    <p class="mb-0">We need to know you can follow along with the labs.</p>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label">Do you own a laptop? <span class="required">*</span></label>
                                    <select name="tech_has_laptop" class="form-select toggle-field" data-toggle-target="laptop-specs" required>
                                        <option value="">Select</option>
                                        @foreach(($options['yes_no'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('tech_has_laptop') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-8 collapse-target laptop-specs {{ old('tech_has_laptop') === 'yes' ? 'is-visible' : '' }}">
                                    <label class="form-label">Laptop specifications (e.g. 8GB RAM / 256GB SSD / Core i5) <span class="required">*</span></label>
                                    <input type="text" name="tech_laptop_specs" class="form-control"
                                           value="{{ old('tech_laptop_specs') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Internet access <span class="required">*</span></label>
                                    <select name="tech_internet" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['internet_quality'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('tech_internet') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Have you used any of these before? (Select all that apply)</label>
                                    <div class="row g-2">
                                        @foreach(($options['tech_tools'] ?? []) as $value => $label)
                                            <div class="col-md-6">
                                                <label class="form-check form-check-custom mb-0">
                                                    <input class="form-check-input" type="checkbox" name="tech_tools[]"
                                                           value="{{ $value }}" {{ in_array($value, $selectedTools, true) ? 'checked' : '' }}>
                                                    <span class="form-check-label">{{ $label }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Do you have any IT / tech experience? If yes, describe briefly.</label>
                                    <textarea name="tech_experience" rows="3" class="form-control">{{ old('tech_experience') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Motivation --}}
                        <div class="application-section">
                            <div class="application-section__header">
                                <span class="pill">5</span>
                                <div>
                                    <h3>Motivation & Goals</h3>
                                    <p class="mb-0">We’re looking for driven, self-aware learners.</p>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label">Why do you want to join this cybersecurity scholarship bootcamp? <span class="required">*</span></label>
                                    <textarea name="motivation_reason" rows="4" class="form-control" required>{{ old('motivation_reason') }}</textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Where do you see yourself in the next 1–2 years in tech or cybersecurity? <span class="required">*</span></label>
                                    <textarea name="motivation_future" rows="3" class="form-control" required>{{ old('motivation_future') }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Have you participated in any tech/cybersecurity training before? <span class="required">*</span></label>
                                    <select name="motivation_prev_training" class="form-select toggle-field" data-toggle-target="training-details" required>
                                        <option value="">Select</option>
                                        @foreach(($options['yes_no'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('motivation_prev_training') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 collapse-target training-details {{ old('motivation_prev_training') === 'yes' ? 'is-visible' : '' }}">
                                    <label class="form-label">If yes, specify the training</label>
                                    <input type="text" name="motivation_prev_details" class="form-control"
                                           value="{{ old('motivation_prev_details') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">What would you do if you are not selected? <span class="required">*</span></label>
                                    <select name="motivation_unselected_plan" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['motivation_unselected_plan'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('motivation_unselected_plan') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Which area of cybersecurity interests you most? <span class="required">*</span></label>
                                    <select name="motivation_interest_area" class="form-select toggle-field" data-toggle-target="interest-other" required>
                                        <option value="">Select</option>
                                        @foreach(($options['motivation_interest_areas'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('motivation_interest_area') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" name="motivation_interest_other"
                                           class="form-control mt-2 collapse-target interest-other {{ old('motivation_interest_area') === 'other' ? 'is-visible' : '' }}"
                                           placeholder="Tell us what else interests you"
                                           value="{{ old('motivation_interest_other') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Skills --}}
                        <div class="application-section">
                            <div class="application-section__header">
                                <span class="pill">6</span>
                                <div>
                                    <h3>Skill Level (Self Assessment)</h3>
                                    <p class="mb-0">There are no wrong answers—this just helps us tailor support.</p>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">How would you describe your current computer skill level? <span class="required">*</span></label>
                                    <select name="skill_level" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['skill_levels'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('skill_level') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">If given a cybersecurity project, which best describes you? <span class="required">*</span></label>
                                    <select name="skill_project_response" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['skill_project_responses'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('skill_project_response') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">How familiar are you with cybersecurity concepts? <span class="required">*</span></label>
                                    <select name="skill_familiarity" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['skill_familiarity'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('skill_familiarity') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Attitude --}}
                        <div class="application-section">
                            <div class="application-section__header">
                                <span class="pill">7</span>
                                <div>
                                    <h3>Attitude & Community Fit</h3>
                                    <p class="mb-0">We value teamwork, communication, and discipline.</p>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label">How do you handle group work or deadlines? <span class="required">*</span></label>
                                    <textarea name="attitude_teamwork" rows="3" class="form-control" required>{{ old('attitude_teamwork') }}</textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Will you actively participate (camera on, speak, collaborate)? <span class="required">*</span></label>
                                    <select name="attitude_participation" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['yes_no'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('attitude_participation') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">How did you hear about this bootcamp? <span class="required">*</span></label>
                                    <select name="attitude_discovery_channel" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['discovery_channels'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('attitude_discovery_channel') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">If selected, are you willing to sign a commitment agreement? <span class="required">*</span></label>
                                    <select name="attitude_commitment" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['yes_no'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('attitude_commitment') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Bonus --}}
                        <div class="application-section">
                            <div class="application-section__header">
                                <span class="pill">8</span>
                                <div>
                                    <h3>Bonus Challenge</h3>
                                    <p class="mb-0">Optional but gives you extra points.</p>
                                </div>
                            </div>
                            <div class="row g-4 align-items-end">
                                <div class="col-md-8">
                                    <label class="form-label">Would you complete a pre-selection cybersecurity challenge if we sent one? <span class="required">*</span></label>
                                    <select name="bonus_willing_challenge" class="form-select" required>
                                        <option value="">Select</option>
                                        @foreach(($options['yes_no'] ?? []) as $value => $label)
                                            <option value="{{ $value }}" @selected(old('bonus_willing_challenge') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <small class="text-muted d-block mb-2">Serious applicants usually say yes 😉</small>
                                </div>
                            </div>
                        </div>

                        <div class="application-submit row g-3 align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <x-honeypot />
                                    <p class="text-muted small mb-0">Submitting this form means you agree to our participation rules and code of conduct.</p>
                                </div>
                            </div>
                            <div class="col-lg-4 text-lg-end">
                                <button type="submit" class="tj-primary-btn w-100">
                                    <span class="btn-text"><span>Submit application</span></span>
                                    <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .application-hero {
            background: radial-gradient(circle at top right, rgba(13,110,253,.08), transparent 55%),
                linear-gradient(135deg, #ffffff 0%, #f5f8ff 100%);
            border-radius: 32px;
            padding: 2.5rem;
            border: 1px solid #e5ecff;
            box-shadow: 0 20px 60px rgba(15,23,42,.08);
        }
        .application-hero__content h1 {
            font-size: clamp(1.8rem, 2.6vw, 2.6rem);
            font-weight: 700;
        }
        .application-hero__meta {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 1.25rem;
        }
        .application-hero__meta > div {
            background: #fff;
            border-radius: 16px;
            padding: 1rem 1.25rem;
            border: 1px solid #eef2ff;
        }
        .application-hero__meta .label {
            font-size: .85rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #6c7a99;
        }

        .application-form {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        .application-section {
            border: 1px solid #eef1fb;
            border-radius: 24px;
            padding: 1.75rem 2rem;
            background: #fff;
            box-shadow: 0 10px 35px rgba(15,23,42,.04);
        }
        .application-section__header {
            display: flex;
            align-items: start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .application-section__header .pill {
            width: 44px;
            height: 44px;
            border-radius: 15px;
            background: #0d99fdff;
            color: #fff;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .application-section__header h3 {
            margin-bottom: .1rem;
        }
        .form-check-custom {
            padding: .75rem 1rem;
            border-radius: 12px;
            border: 1px solid #e5e9f5;
            background: #fdfdff;
            display: flex;
            align-items: center;
            gap: .75rem;
        }
        .form-check-custom input {
            width: 1.15rem;
            height: 1.15rem;
        }
        .collapse-target {
            display: none;
        }
        .collapse-target.is-visible {
            display: block;
        }
        .application-submit {
            border: 1px dashed #d6ddf4;
            border-radius: 24px;
            padding: 1.5rem 2rem;
            background: #f7f9ff;
        }
        @media (max-width: 767px) {
            .application-section {
                padding: 1.25rem;
            }
            .application-hero {
                padding: 1.75rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.application-form');
            if (form) {
                const showBanner = (message) => {
                    let banner = document.getElementById('application-error-banner');
                    if (!banner) {
                        banner = document.createElement('div');
                        banner.id = 'application-error-banner';
                        banner.className = 'alert alert-danger rounded-4 border-0 shadow-sm mb-4';
                        form.parentNode.insertBefore(banner, form);
                    }
                    banner.textContent = message;
                };

                const focusFirstInvalid = () => {
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({behavior: 'smooth', block: 'center'});
                        firstInvalid.focus({preventScroll: true});
                    }
                };

                form.addEventListener('invalid', function (event) {
                    event.preventDefault();
                    showBanner('Please complete the highlighted fields before submitting.');
                    focusFirstInvalid();
                }, true);

                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        showBanner('Please complete the highlighted fields before submitting.');
                        focusFirstInvalid();
                    }
                });
            }

            function toggleTargets(select) {
                const targetClass = select.dataset.toggleTarget;
                if (!targetClass) return;
                const targets = document.querySelectorAll('.' + targetClass);
                targets.forEach(target => {
                    if (select.value === 'yes' || select.value === 'other') {
                        target.classList.add('is-visible');
                    } else {
                        target.classList.remove('is-visible');
                        target.querySelectorAll('input, textarea').forEach(el => {
                            el.value = '';
                        });
                    }
                });
            }

            document.querySelectorAll('.toggle-field').forEach(select => {
                select.addEventListener('change', () => toggleTargets(select));
                toggleTargets(select);
            });
        });
    </script>
@endpush
