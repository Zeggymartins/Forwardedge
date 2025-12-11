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
@endphp
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">🎓 Scholarship Applications</h1>
            <p class="text-muted mb-0">Track, approve, or reject scholarship submissions.</p>
        </div>
        <form action="{{ route('admin.scholarships.applications') }}" method="GET" class="d-flex align-items-center gap-2">
            <label for="per_page" class="text-muted small mb-0">Per page</label>
            <select name="per_page" id="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach($perPageOptions ?? [10,20,50,100] as $option)
                    <option value="{{ $option }}" @selected(($perPage ?? 20) == $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="bg-gradient-primary text-white">
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
                                <span class="badge rounded-pill
                                    @class([
                                        'bg-warning text-dark' => $application->status === 'pending',
                                        'bg-success' => $application->status === 'approved',
                                        'bg-danger' => $application->status === 'rejected',
                                    ])">
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
                                    <div class="modal-header bg-gradient-primary text-white">
                                        <h5 class="modal-title">Application #{{ $application->id }}</h5>
                                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                            <td colspan="6" class="text-center py-5 text-muted">No applications yet.</td>
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
        <div>
            {{ $applications->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .mini-list {
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }
    .mini-list li {
        margin-bottom: .35rem;
    }
</style>
@endpush
