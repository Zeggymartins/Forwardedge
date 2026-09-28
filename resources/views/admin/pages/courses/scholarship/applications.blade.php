@extends('admin.master_page')

@section('title', 'Scholarship Applications')

@section('main')
@php
    use Illuminate\Support\Str;

    $scholarshipOptions = config('scholarship.form_options', []);
    $optionLabel = function (string $group, ?string $value) use ($scholarshipOptions) {
        if (!$value) {
            return '—';
        }
        return $scholarshipOptions[$group][$value] ?? Str::headline(str_replace('_', ' ', $value));
    };
    $activeFilters = collect();
    if ($nameEmail) $activeFilters->push(['label' => 'Applicant', 'value' => $nameEmail]);
    if ($dateFrom || $dateTo) $activeFilters->push(['label' => 'Submitted', 'value' => trim(($dateFrom ?: 'Any') . ' - ' . ($dateTo ?: 'Any'))]);
    if ($scoreMin !== null && $scoreMin !== '') $activeFilters->push(['label' => 'Min score', 'value' => $scoreMin]);
    if ($scoreMax !== null && $scoreMax !== '') $activeFilters->push(['label' => 'Max score', 'value' => $scoreMax]);
    if ($scoreSort) $activeFilters->push(['label' => 'Score sort', 'value' => $scoreSort === 'asc' ? 'Lowest first' : 'Highest first']);
    if ($status) $activeFilters->push(['label' => 'Status', 'value' => ucfirst($status)]);
    if ($discoveryChannel) $activeFilters->push(['label' => 'Referral', 'value' => $optionLabel('discovery_channels', $discoveryChannel)]);
    if (!empty($countries ?? [])) $activeFilters->push(['label' => 'Countries', 'value' => implode(', ', $countries)]);
    if (($perPage ?? 20) !== 20) $activeFilters->push(['label' => 'Per page', 'value' => $perPage]);
@endphp
<div class="container py-4">
    <div class="pagetitle">
        <div class="pagetitle-left">
            <div class="pagetitle-icon"><i class="bi bi-mortarboard"></i></div>
            <div>
                <h1>Scholarship Applications</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Academy</a></li>
                        <li class="breadcrumb-item active">Scholarship Applications</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold px-3 py-2">
                {{ number_format($applications->total()) }} total
            </span>
            <a href="{{ route('admin.scholarships.export', request()->query()) }}" class="btn btn-success btn-sm">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
        </div>
    </div>
    <div class="mb-4">
        <form action="{{ route('admin.scholarships.applications') }}" method="GET" class="w-100 scholarship-filters">
            <div class="card border-0 shadow-sm filter-shell">
                <div class="card-body p-3 p-lg-4">
                    <div class="filter-toolbar mb-3">
                        <div>
                            <div class="filter-title">Application Filters</div>
                            <div class="filter-subtitle">Search by applicant, score, decision, referral source, country, or submission date.</div>
                        </div>
                        <div class="filter-toolbar-meta">
                            <span class="filter-stat">
                                <strong>{{ $applications->count() }}</strong>
                                <span>shown</span>
                            </span>
                            <span class="filter-stat">
                                <strong>{{ $activeFilters->count() }}</strong>
                                <span>active</span>
                            </span>
                        </div>
                    </div>

                    @if($activeFilters->isNotEmpty())
                        <div class="active-filter-strip mb-3">
                            @foreach($activeFilters as $filter)
                                <span class="active-filter-chip">
                                    <span class="active-filter-label">{{ $filter['label'] }}</span>
                                    <span>{{ $filter['value'] }}</span>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="filter-grid">
                        <div class="filter-panel filter-panel-search">
                            <label class="form-label mb-2" for="filter_name_email">Applicant</label>
                            <input type="text"
                                   id="filter_name_email"
                                   name="name_email"
                                   class="form-control filter-input"
                                   placeholder="Name or email"
                                   value="{{ $nameEmail }}">
                        </div>

                        <div class="filter-panel">
                            <label class="form-label mb-2">Submitted Date</label>
                            <div class="filter-inline-grid">
                                <div>
                                    <label class="form-label filter-sub-label mb-1" for="filter_date_from">From</label>
                                    <input type="date"
                                           id="filter_date_from"
                                           name="date_from"
                                           class="form-control filter-input"
                                           value="{{ $dateFrom }}">
                                </div>
                                <div>
                                    <label class="form-label filter-sub-label mb-1" for="filter_date_to">To</label>
                                    <input type="date"
                                           id="filter_date_to"
                                           name="date_to"
                                           class="form-control filter-input"
                                           value="{{ $dateTo }}">
                                </div>
                            </div>
                        </div>

                        <div class="filter-panel">
                            <label class="form-label mb-2">Score Range</label>
                            <div class="filter-inline-grid">
                                <div>
                                    <label class="form-label filter-sub-label mb-1" for="filter_score_min">Min</label>
                                    <input type="number"
                                           id="filter_score_min"
                                           name="score_min"
                                           class="form-control filter-input"
                                           placeholder="0"
                                           value="{{ $scoreMin }}">
                                </div>
                                <div>
                                    <label class="form-label filter-sub-label mb-1" for="filter_score_max">Max</label>
                                    <input type="number"
                                           id="filter_score_max"
                                           name="score_max"
                                           class="form-control filter-input"
                                           placeholder="100"
                                           value="{{ $scoreMax }}">
                                </div>
                            </div>
                        </div>

                        <div class="filter-panel filter-panel-wide">
                            <label class="form-label mb-2">Decision Status</label>
                            <div class="status-chip-row">
                                @foreach(['' => 'Any', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                                    <label class="status-chip {{ ($status ?? '') === $value ? 'is-active' : '' }}">
                                        <input type="radio" name="status" value="{{ $value }}" @checked(($status ?? '') === $value)>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="filter-panel">
                            <label class="form-label mb-2" for="filter_score_sort">Score Sort</label>
                            <select name="score_sort" id="filter_score_sort" class="form-select filter-select">
                                <option value="">Newest first</option>
                                <option value="asc" @selected($scoreSort === 'asc')>Score: low to high</option>
                                <option value="desc" @selected($scoreSort === 'desc')>Score: high to low</option>
                            </select>
                        </div>

                        <div class="filter-panel">
                            <label class="form-label mb-2" for="filter_discovery_channel">Referral Source</label>
                            <select name="discovery_channel" id="filter_discovery_channel" class="form-select filter-select">
                                <option value="">Any</option>
                                @foreach(($scholarshipOptions['discovery_channels'] ?? []) as $value => $label)
                                    <option value="{{ $value }}" @selected(($discoveryChannel ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-panel">
                            <label class="form-label mb-2" for="per_page">Per Page</label>
                            <select name="per_page" id="per_page" class="form-select filter-select">
                                @foreach($perPageOptions ?? [10,20,50,100] as $option)
                                    <option value="{{ $option }}" @selected(($perPage ?? 20) == $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-panel filter-panel-countries">
                            <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                <label class="form-label mb-0">Countries</label>
                                <div class="country-actions">
                                    <button type="button" class="btn btn-link btn-sm p-0 country-toggle" data-country-mode="all">All</button>
                                    <button type="button" class="btn btn-link btn-sm p-0 country-toggle" data-country-mode="none">None</button>
                                </div>
                            </div>
                            <div class="country-chip-grid">
                                @foreach(($allCountries ?? []) as $countryOption)
                                    <label class="country-chip {{ in_array($countryOption, $countries ?? []) ? 'is-active' : '' }}">
                                        <input type="checkbox" name="country[]" value="{{ $countryOption }}" @checked(in_array($countryOption, $countries ?? []))>
                                        <span>{{ $countryOption }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="filter-panel filter-panel-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('admin.scholarships.applications') }}" class="btn btn-light">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="py-3 px-4">Applicant</th>
                        <th class="py-3 px-4">Course</th>
                        <th class="py-3 px-4">Schedule</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Score</th>
                        <th class="py-3 px-4">Submitted</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $application)
                        @php
                            $formData = $application->form_data ?? [];
                            $contact = $formData['contact'] ?? [];
                            $personal = $formData['personal'] ?? [];
                            $education = $formData['education'] ?? [];
                            $commitment = $formData['commitment'] ?? [];
                            $technical = $formData['technical'] ?? [];
                            $motivation = $formData['motivation'] ?? [];
                            $skills = $formData['skills'] ?? [];
                            $attitude = $formData['attitude'] ?? [];
                            $bonus = $formData['bonus'] ?? [];
                        @endphp
                        <tr class="table-row-hover">
                            <td class="py-3 px-4">
                                <strong>{{ $application->user->name ?? $contact['name'] ?? '—' }}</strong><br>
                                <small class="text-muted">{{ $application->user->email ?? $contact['email'] ?? '—' }}</small>
                            </td>
                            <td class="py-3 px-4">
                                {{ $application->course->title ?? '—' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($application->schedule)
                                    {{ optional($application->schedule->start_date)->format('M j, Y') ?? 'TBA' }}
                                    –
                                    {{ optional($application->schedule->end_date)->format('M j, Y') ?? 'TBA' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $statusClass = match($application->status) {
                                        'approved' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                        'pending' => 'bg-warning text-dark',
                                        default => 'bg-secondary text-white',
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $statusClass }}">
                                    {{ ucfirst($application->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="d-flex flex-column gap-1">
                                    <strong>{{ $application->score ?? '—' }}</strong>
                                    @if($application->auto_decision)
                                        <span class="badge rounded-pill
                                            @class([
                                                'bg-success' => $application->auto_decision === 'approve',
                                                'bg-danger' => $application->auto_decision === 'reject',
                                                'bg-secondary' => $application->auto_decision === 'pending',
                                            ])">
                                            Auto: {{ ucfirst($application->auto_decision) }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary-subtle text-muted">Manual</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                {{ $application->created_at?->format('M j, Y g:i A') ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="d-flex flex-wrap gap-2 justify-content-center">
                                    <button class="btn btn-sm btn-outline-info"
                                            data-bs-toggle="modal"
                                            data-bs-target="#viewApplication{{ $application->id }}">
                                        View
                                    </button>
                                    @if($application->status !== 'approved')
                                        <form action="{{ route('admin.scholarships.approve', $application) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-sm btn-success"
                                                    onclick="return confirm('Approve this application?')">
                                                Approve
                                            </button>
                                        </form>
                                    @endif
                                    @if($application->status !== 'rejected')
                                        <button class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rejectApplication{{ $application->id }}">
                                            Reject
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- View modal --}}
                        <div class="modal fade" id="viewApplication{{ $application->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Application #{{ $application->id }}</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <h6 class="fw-semibold mb-2">Applicant</h6>
                                                <p class="mb-1">{{ $application->user->name ?? $contact['name'] ?? '—' }}</p>
                                                <p class="mb-1 text-muted">{{ $application->user->email ?? $contact['email'] ?? '—' }}</p>
                                                <p class="mb-0 text-muted">{{ $application->user->phone ?? $contact['phone'] ?? '—' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="fw-semibold mb-2">Course</h6>
                                                <p class="mb-1">{{ $application->course->title ?? '—' }}</p>
                                                @if($application->schedule)
                                                    <p class="mb-0 text-muted">
                                                        {{ optional($application->schedule->start_date)->format('M j, Y') ?? 'TBA' }}
                                                        –
                                                        {{ optional($application->schedule->end_date)->format('M j, Y') ?? 'TBA' }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="col-12">
                                                <div class="alert bg-light border rounded-3 d-flex flex-wrap justify-content-between gap-3">
                                                    <div>
                                                        <strong>Score:</strong> {{ $application->score ?? '—' }}<br>
                                                        <strong>Auto decision:</strong> {{ ucfirst($application->auto_decision ?? 'manual') }}
                                                    </div>
                                                    @if($application->decision_notes)
                                                        <div class="text-muted small">
                                                            <strong>Notes:</strong> {{ $application->decision_notes }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <h6 class="fw-semibold mb-2">Personal</h6>
                                                <ul class="mini-list mb-3">
                                                    <li><strong>Full name:</strong> {{ $personal['full_name'] ?? $contact['name'] ?? '—' }}</li>
                                                    <li><strong>Email:</strong> {{ $personal['email'] ?? $contact['email'] ?? '—' }}</li>
                                                    <li><strong>Phone:</strong> {{ $personal['phone'] ?? $contact['phone'] ?? '—' }}</li>
                                                    <li><strong>Location:</strong> {{ $personal['location'] ?? '—' }}</li>
                                                    <li><strong>Gender:</strong> {{ $optionLabel('genders', $personal['gender'] ?? null) }}</li>
                                                    <li><strong>Age range:</strong> {{ $optionLabel('age_ranges', $personal['age_range'] ?? null) }}</li>
                                                </ul>

                                                <h6 class="fw-semibold mb-2">Education</h6>
                                                <ul class="mini-list mb-3">
                                                    <li><strong>Highest level:</strong> {{ $optionLabel('education_levels', $education['highest_level'] ?? null) }}</li>
                                                    <li><strong>Field / background:</strong> {{ $education['field'] ?? '—' }}</li>
                                                    <li><strong>Currently in school:</strong> {{ $optionLabel('yes_no', $education['currently_in_school'] ?? null) }}</li>
                                                    @if(($education['currently_in_school'] ?? null) === 'yes')
                                                        <li><strong>Institution:</strong> {{ $education['institution'] ?? '—' }}</li>
                                                        <li><strong>Level:</strong> {{ $education['institution_level'] ?? '—' }}</li>
                                                    @endif
                                                </ul>

                                                <h6 class="fw-semibold mb-2">Motivation & Goals</h6>
                                                <p class="mb-2">{{ $motivation['reason'] ?? $formData['why_join'] ?? '—' }}</p>
                                                <ul class="mini-list mb-3">
                                                    <li><strong>Future plan:</strong> {{ $motivation['future_plan'] ?? '—' }}</li>
                                                    <li><strong>If not selected:</strong> {{ $optionLabel('motivation_unselected_plan', $motivation['plan_if_not_selected'] ?? null) }}</li>
                                                    <li><strong>Interest area:</strong>
                                                        {{ $optionLabel('motivation_interest_areas', $motivation['interest_area'] ?? null) }}
                                                        @if(($motivation['interest_area'] ?? null) === 'other')
                                                            – {{ $motivation['interest_area_other'] ?? 'N/A' }}
                                                        @endif
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-12">
                                                <h6 class="fw-semibold mb-2">Commitment & Technical readiness</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <ul class="mini-list">
                                                            <li><strong>Availability:</strong> {{ $optionLabel('commit_availability', $commitment['availability'] ?? null) }}</li>
                                                            <li><strong>Hours / week:</strong> {{ $optionLabel('commit_hours', $commitment['hours_per_week'] ?? null) }}</li>
                                                            <li><strong>Consistency plan:</strong> {{ $commitment['consistency_plan'] ?? '—' }}</li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <ul class="mini-list">
                                                            <li><strong>Laptop:</strong> {{ $optionLabel('yes_no', $technical['has_laptop'] ?? null) }}</li>
                                                            <li><strong>Specs:</strong> {{ $technical['laptop_specs'] ?? '—' }}</li>
                                                            <li><strong>Internet:</strong> {{ $optionLabel('internet_quality', $technical['internet'] ?? null) }}</li>
                                                            <li><strong>Tools used:</strong>
                                                                @php
                                                                    $tools = collect($technical['tools'] ?? [])
                                                                        ->map(fn($tool) => $optionLabel('tech_tools', $tool))
                                                                        ->filter()
                                                                        ->implode(', ');
                                                                @endphp
                                                                {{ $tools ?: '—' }}
                                                            </li>
                                                            <li><strong>Experience:</strong> {{ $technical['experience'] ?? $formData['experience'] ?? '—' }}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <h6 class="fw-semibold mb-2">Skills & Attitude</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <ul class="mini-list">
                                                            <li><strong>Skill level:</strong> {{ $optionLabel('skill_levels', $skills['level'] ?? null) }}</li>
                                                            <li><strong>Project response:</strong> {{ $optionLabel('skill_project_responses', $skills['project_response'] ?? null) }}</li>
                                                            <li><strong>Familiarity:</strong> {{ $optionLabel('skill_familiarity', $skills['familiarity'] ?? null) }}</li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <ul class="mini-list">
                                                            <li><strong>Teamwork:</strong> {{ $attitude['teamwork'] ?? '—' }}</li>
                                                            <li><strong>Participation:</strong> {{ $optionLabel('yes_no', $attitude['participation'] ?? null) }}</li>
                                                            <li><strong>Discovery channel:</strong> {{ $optionLabel('discovery_channels', $attitude['discovery_channel'] ?? null) }}</li>
                                                            <li><strong>Commitment agreement:</strong> {{ $optionLabel('yes_no', $attitude['commitment_agreement'] ?? null) }}</li>
                                                            <li><strong>Bonus challenge:</strong> {{ $optionLabel('yes_no', $bonus['challenge_opt_in'] ?? null) }}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($application->admin_notes)
                                                <div class="col-12">
                                                    <h6 class="fw-semibold mb-2">Admin Notes</h6>
                                                    <p>{{ $application->admin_notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Reject modal --}}
                        <div class="modal fade" id="rejectApplication{{ $application->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Application</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.scholarships.reject', $application) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Notes (optional)</label>
                                                <textarea class="form-control" name="notes" rows="3"
                                                          placeholder="Share a short reason or next steps"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-danger">Reject Application</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No applications yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="text-muted small">
            Showing
            <strong>{{ $applications->firstItem() ?? 0 }}-{{ $applications->lastItem() ?? 0 }}</strong>
            of
            <strong>{{ $applications->total() }}</strong>
            applications
        </div>
        @if ($applications->hasPages())
            <nav class="d-inline-flex align-items-center gap-2">
                <a href="{{ $applications->previousPageUrl() ?: '#' }}"
                   class="btn btn-sm btn-outline-secondary @if(!$applications->previousPageUrl()) disabled @endif">
                    Previous
                </a>
                <span class="text-muted small">
                    Page <strong>{{ $applications->currentPage() }}</strong> of <strong>{{ $applications->lastPage() }}</strong>
                </span>
                <a href="{{ $applications->nextPageUrl() ?: '#' }}"
                   class="btn btn-sm btn-outline-secondary @if(!$applications->hasMorePages()) disabled @endif">
                    Next
                </a>
            </nav>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .mini-list { list-style: none; padding-left: 0; margin-bottom: 0; }
    .mini-list li { margin-bottom: .35rem; }

    /* Filter shell */
    .scholarship-filters .filter-shell {
        border: 1px solid rgba(15,23,42,.08);
        border-radius: 12px;
        background: #fff;
    }
    .filter-toolbar {
        display: flex; flex-wrap: wrap; align-items: flex-start;
        justify-content: space-between; gap: 1rem;
    }
    .filter-title { font-weight: 700; font-size: .95rem; color: #0f172a; }
    .filter-subtitle { font-size: .83rem; color: #64748b; }
    .filter-toolbar-meta { display: flex; gap: .6rem; flex-wrap: wrap; }
    .filter-stat {
        min-width: 80px; display: grid; gap: .1rem;
        padding: .55rem .75rem; border-radius: 10px;
        background: #f8fafc; border: 1px solid rgba(15,23,42,.08); text-align: center;
    }
    .filter-stat strong { color: #0f172a; font-size: .95rem; line-height: 1; }
    .filter-stat span { color: #64748b; font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; }

    /* Active filter chips */
    .active-filter-strip { display: flex; flex-wrap: wrap; gap: .5rem; }
    .active-filter-chip {
        display: inline-flex; flex-wrap: wrap; gap: .3rem; align-items: center;
        padding: .3rem .6rem; border-radius: 999px;
        background: rgba(14,165,233,.08); border: 1px solid rgba(14,165,233,.2);
        color: #334155; font-size: .8rem;
    }
    .active-filter-label { font-weight: 600; color: #0f172a; }

    /* Filter grid */
    .filter-grid { display: grid; grid-template-columns: repeat(12, minmax(0,1fr)); gap: .85rem; }
    .filter-panel {
        grid-column: span 3; padding: .85rem;
        border: 1px solid rgba(15,23,42,.08); border-radius: 10px; background: #f8fafc;
    }
    .filter-panel-search  { grid-column: span 4; }
    .filter-panel-wide    { grid-column: span 6; }
    .filter-panel-countries { grid-column: span 9; }
    .filter-panel-actions {
        grid-column: span 3; display: grid; align-content: end; gap: .6rem;
    }
    .filter-panel .form-label { font-weight: 600; font-size: .83rem; color: #334155; }
    .filter-sub-label { font-weight: 500; color: #64748b; font-size: .72rem; text-transform: uppercase; letter-spacing: .06em; }
    .filter-inline-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: .6rem; }

    /* Inputs inside filter — match design system */
    .filter-input, .filter-select {
        min-height: 40px; border-radius: 10px;
        border: 1.5px solid rgba(148,163,184,.45); background: #fff;
    }
    .filter-input:focus, .filter-select:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14,165,233,.15);
    }

    /* Status chips (radio) */
    .status-chip-row { display: flex; flex-wrap: wrap; gap: .5rem; }
    .status-chip, .country-chip { position: relative; display: inline-flex; align-items: center; }
    .status-chip input, .country-chip input { position: absolute; opacity: 0; pointer-events: none; }
    .status-chip span, .country-chip span {
        display: inline-flex; align-items: center; justify-content: center;
        min-height: 36px; padding: .4rem .85rem; border-radius: 999px;
        border: 1.5px solid rgba(15,23,42,.12); background: #fff;
        color: #475569; font-weight: 600; font-size: .83rem;
        transition: all .15s ease; cursor: pointer;
    }
    .status-chip.is-active span,
    .status-chip input:checked + span {
        background: #0ea5e9; border-color: #0ea5e9; color: #fff;
        box-shadow: 0 4px 12px rgba(14,165,233,.25);
    }

    /* Country chips (checkbox) */
    .country-actions { display: inline-flex; gap: .5rem; }
    .country-toggle { color: #0ea5e9; text-decoration: none; font-weight: 600; font-size: .8rem; }
    .country-chip-grid {
        display: flex; flex-wrap: wrap; gap: .45rem;
        max-height: 170px; overflow-y: auto; padding-right: .2rem;
    }
    .country-chip span { min-height: 34px; padding: .35rem .75rem; border-radius: 8px; font-size: .8rem; }
    .country-chip.is-active span,
    .country-chip input:checked + span {
        background: rgba(14,165,233,.1); border-color: rgba(14,165,233,.35); color: #0369a1;
    }

    @media (max-width: 1199.98px) {
        .filter-panel, .filter-panel-search, .filter-panel-wide,
        .filter-panel-countries, .filter-panel-actions { grid-column: span 6; }
    }
    @media (max-width: 767.98px) {
        .filter-panel, .filter-panel-search, .filter-panel-wide,
        .filter-panel-countries, .filter-panel-actions { grid-column: 1 / -1; }
        .filter-inline-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.country-toggle').forEach((btn) => {
        btn.addEventListener('click', () => {
            const mode = btn.dataset.countryMode;
            document.querySelectorAll('.country-chip input[type="checkbox"]').forEach((input) => {
                input.checked = mode === 'all';
                input.closest('.country-chip')?.classList.toggle('is-active', input.checked);
            });
        });
    });

    document.querySelectorAll('.country-chip input[type="checkbox"]').forEach((input) => {
        input.addEventListener('change', () => {
            input.closest('.country-chip')?.classList.toggle('is-active', input.checked);
        });
    });

    document.querySelectorAll('.status-chip input[type="radio"]').forEach((input) => {
        input.addEventListener('change', () => {
            document.querySelectorAll('.status-chip').forEach((chip) => chip.classList.remove('is-active'));
            input.closest('.status-chip')?.classList.add('is-active');
        });
    });
});
</script>
@endpush
